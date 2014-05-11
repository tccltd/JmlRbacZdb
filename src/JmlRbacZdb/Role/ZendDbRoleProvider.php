<?php

namespace JmlRbacZdb\Role;

use Rbac\Role\Role;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use ZfcRbac\Role\RoleProviderInterface;

/**
 * Zend Db provider
 * @author jmleroux
 */
class ZendDbRoleProvider implements RoleProviderInterface
{
    const ERROR_MISSING_ROLE = 'One or more role not found.';

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var ZendDbRoleProviderOptions
     */
    protected $options;

    /**
     * @var array
     */
    protected $roles;

    public function __construct(Adapter $adapter, array $options)
    {
        $this->adapter = $adapter;
        $this->options = new ZendDbRoleProviderOptions($options);
    }

    /**
     * @param array $roleNames
     * @return array|\Rbac\Role\RoleInterface[]
     * @throws \DomainException
     */
    public function getRoles(array $roleNames)
    {
        $sql = new Sql($this->adapter);
        $options = $this->options;
        $select = $sql->select($options->getRoleTable());
        $select->where([$options->getRoleNameColumn() => $roleNames]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $roles = [];
        foreach ($statement->execute() as $row) {
            $role = new Role($row[$options->getRoleNameColumn()]);
            $this->getRolePermissions($role);
            $roles[] = $role;
        }
        if (count($roles) < count($roleNames)) {
            throw new \DomainException(self::ERROR_MISSING_ROLE);
        }
        return $roles;
    }

    protected function getRolePermissions(Role $role)
    {
        $sql = new Sql($this->adapter);
        $options = $this->options;
        $select = $sql->select(['rp' => $options->getRolePermissionTable()]);
        $select->join(
            ['p' => $options->getPermissionTable()],
            sprintf('rp.%s = p.%s', $options->getPermissionJoinColumn(), $options->getPermissionNameColumn()),
            []
        );
        $select->columns([$options->getPermissionJoinColumn()]);
        $select->where(['rp.' . $options->getRoleJoinColumn() => $role->getName()]);
        $statement = $sql->prepareStatementForSqlObject($select);
        foreach ($statement->execute() as $permission) {
            $role->addPermission($permission[$options->getPermissionJoinColumn()]);
        }
        return $role;
    }
}
