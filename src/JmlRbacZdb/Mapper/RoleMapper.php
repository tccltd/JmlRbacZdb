<?php
namespace JmlRbacZdb\Mapper;

use Rbac\Role\HierarchicalRole;
use Zend\Db\Sql\Select;
use Zend\Stdlib\ArrayUtils;
use ZfcRbac\Identity\IdentityInterface;
use Zend\Db\Sql\Predicate\In;

// TODO: Recursive role detection.
class RoleMapper
{
    protected $tableName  = 'user_user_role';

    protected $sql;
    protected $hydratingResultSetPrototype;

    public function __construct($sql, $hydratingResultSetPrototype)
    {
        $this->setSql($sql);
        $this->setHydratingResultSetPrototype($hydratingResultSetPrototype);
    }

    public function fetchAll()
    {
        // Get roles and their parents from the database.
        $sql = $this->getSql();
        $select = $sql->select()
            ->columns([ 'id', 'role' ])
            ->from('user_role')
            ->join('user_role_parent', 'user_role.id=user_role_parent.role_id', [ 'parent_role_id' ], Select::JOIN_LEFT);
        $rolesAndParents = $sql->prepareStatementForSqlObject($select)->execute();

        // Get permissions from the database.
        $sql = $this->getSql();
        $select = $sql->select()
            ->columns([ 'role_id' ])
            ->from('user_role_permission')
            ->join('user_permission', 'user_role_permission.permission_id=user_permission.id', [ 'permission' => 'name' ], Select::JOIN_LEFT);

        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $permissions = [];
        foreach($results as $row) {
            $permissions[$row['role_id']][$row['permission']] = true;
        }

        $roles = [];
        foreach ($rolesAndParents as $roleAndParents) {
            if (!isset($roles[$roleAndParents['id']])) {
                $roles[$roleAndParents['id']] = [
                    'role'     => new HierarchicalRole($roleAndParents['role']),
                    'parentIds' => [],
                ];
            }
            if (!empty($roleAndParents['parent_role_id'])) {
                $roles[$roleAndParents['id']]['parentIds'][] = $roleAndParents['parent_role_id'];
            }
        }

        $rootRoles = [];
        foreach ($roles as $id => $role) {
            if(isset($permissions[$id])) foreach ($permissions[$id] as $permission => $granted) {
                $role['role']->addPermission($permission);
            }

            if (empty($role['parentIds'])) {
                $rootRoles[] = $role['role'];
            } else {
                foreach ($role['parentIds'] as $parentId) {
                    $roles[$parentId]['role']->addChild($role['role']);
                }
            }
        }

        return $rootRoles;
    }

    // TODO: Will not properly populate hierarchical roles.
    public function findByRoleNames($roleNames)
    {
        // Change $roleNames to array indexed on role (to ensure uniqueness and increase lookup speed).
        $roleNames = array_fill_keys($roleNames, true);

        // TODO: Consider whether this should be extracted out into, perhaps, the Hierarchical Role object.
        $findRoles = function ($allRoles) use (&$findRoles, $roleNames) {
            $roles = [];
            foreach ($allRoles as $role) {
                if (isset($roleNames[$role->getName()])) {
                    $roles[] = $role;
                }

                // Check child roles.
                if ($role->hasChildren()) {
                    $roles = array_merge($roles, $findRoles($role->getChildren()));
                }
            }
            return $roles;
        };

        // Return all Role objects identified by the specified role names.
        return $findRoles($this->fetchAll());
    }

    public function populateUserWithRoles(IdentityInterface $user)
    {
        $userId = $user->getId();

        $sql = $this->getSql();
        $select = $sql->select()
            ->columns([])
            ->from('user')
            ->join('user_user_role', 'user.user_id=user_user_role.user_id', [])
            ->join('user_role', 'user_user_role.role_id=user_role.id', [ 'role' ])
            ->where([ 'user.user_id ' => $userId ]);

        $roles = $sql->prepareStatementForSqlObject($select)->execute();
        $roles = array_map(function ($element) { return $element['role']; }, ArrayUtils::iteratorToArray($roles));

        $user->setRoles($roles);
    }

    public function populateRolesWithPermissions(array $roles)
    {
        // Index roles on role ID for easier population.
        $roleIds = [];
        foreach ($roles as $role) {
            $roleIds[$role['role_id']] = $role;
        }

        // Get permissions from the database.
        $sql = $this->getSql();
        $select = $sql->select()
            ->columns([ 'role_id' ])
            ->from('user_role_permission')
            ->join('user_permission', 'user_role_permission.permission_id=user_permission.permission_id', [ 'name' ])
            ->where(new In('user_role_permission.role_id', array_keys($roleIds)));

        $permissions = $sql->prepareStatementForSqlObject($select)->execute();

        // Assign permissions to roles.
        foreach ($permissions as $permission) {
            $roleIds[$permission['role_id']]->setPermission($permission['name']);
        }

        return $roles;
    }

    protected function getSql()
    {
        return $this->sql;
    }

    protected function setSql($sql)
    {
        $this->sql = $sql;
    }

    /**
     * Get Role Hydrator.
     *
     * @return paramType
     */
    public function getHydratingResultSet()
    {
        return clone $this->hydratingResultSetPrototype;
    }

    /**
     * Set Role Hydrator.
     *
     * @param paramType $hydrator
     * @return \fullyQualifiedClassName
     */
    public function setHydratingResultSetPrototype($hydratingResultSetPrototype)
    {
        $this->hydratingResultSetPrototype = $hydratingResultSetPrototype;
        return $this;
    }
}
