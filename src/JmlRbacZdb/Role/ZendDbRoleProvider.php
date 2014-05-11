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

    public function getRoles(array $roleNames)
    {
        $sql = new Sql($this->adapter);
        $options = $this->options;
        $select = $sql->select($options->getTable());
        $select->where(array($options->getNameColumn() => $roleNames));

        $statement = $sql->prepareStatementForSqlObject($select);
        $roles = array();
        foreach ($statement->execute() as $row) {
            $role = new Role($row[$options->getNameColumn()]);
            $roles[] = $role;
        }
        return $roles;
    }
}
