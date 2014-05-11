<?php
namespace JmlRbacZdb\Role;

use Zend\Stdlib\AbstractOptions;

class ZendDbRoleProviderOptions extends AbstractOptions
{
    protected $roleTable = 'role';
    protected $roleNameColumn = 'role_name';

    /**
     * The join column to the parent role. If left empty, then
     * no join is performed. Note: this is a limited implementation and
     * assumes that the join is done on the same table. If this
     * does not work for you create a new provider and use that instead.
     *
     * @var string
     */
    protected $roleParentColumn = 'parent_role';

    protected $permissionTable = 'permission';
    protected $permissionNameColumn = 'permission_name';

    protected $rolePermissionTable = 'role_permission';
    protected $roleJoinColumn = 'role_name';
    protected $permissionJoinColumn = 'permission_name';

    /**
     * @return string
     */
    public function getPermissionJoinColumn()
    {
        return $this->permissionJoinColumn;
    }

    /**
     * @param string $permissionJoinColumn
     */
    public function setPermissionJoinColumn($permissionJoinColumn)
    {
        $this->permissionJoinColumn = $permissionJoinColumn;
    }

    /**
     * @return string
     */
    public function getRoleParentColumn()
    {
        return $this->roleParentColumn;
    }

    /**
     * @param string $roleParentColumn
     */
    public function setRoleParentColumn($roleParentColumn)
    {
        $this->roleParentColumn = $roleParentColumn;
    }

    /**
     * @return string
     */
    public function getRolePermissionTable()
    {
        return $this->rolePermissionTable;
    }

    /**
     * @param string $rolePermissionTable
     */
    public function setRolePermissionTable($rolePermissionTable)
    {
        $this->rolePermissionTable = $rolePermissionTable;
    }

    /**
     * @return string
     */
    public function getPermissionNameColumn()
    {
        return $this->permissionNameColumn;
    }

    /**
     * @param string $permissionNameColumn
     */
    public function setPermissionNameColumn($permissionNameColumn)
    {
        $this->permissionNameColumn = $permissionNameColumn;
    }

    /**
     * @return string
     */
    public function getPermissionTable()
    {
        return $this->permissionTable;
    }

    /**
     * @param string $permissionTable
     */
    public function setPermissionTable($permissionTable)
    {
        $this->permissionTable = $permissionTable;
    }

    public function getRoleJoinColumn()
    {
        return $this->roleJoinColumn;
    }

    /**
     * @param  string $joinColumn
     */
    public function setRoleJoinColumn($joinColumn)
    {
        $this->roleJoinColumn = (string)$joinColumn;
    }

    public function getRoleNameColumn()
    {
        return $this->roleNameColumn;
    }

    /**
     * @param  string $nameColumn
     */
    public function setRoleNameColumn($nameColumn)
    {
        $this->roleNameColumn = (string)$nameColumn;
    }

    public function getRoleTable()
    {
        return $this->roleTable;
    }

    /**
     * @param  string $table
     */
    public function setRoleTable($table)
    {
        $this->roleTable = (string)$table;
    }
}
