<?php

namespace JmlRbacZdb\Role;

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
        $results = $statement->execute();
        var_dump($results);
        exit();

        $sqlPattern = 'SELECT role.%s AS name, parent.%s AS parent
                FROM %s role
                LEFT JOIN %s parent
                ON role.%s = parent.%s';

        $values = array(
            $options->getNameColumn(),
            $options->getNameColumn(),
            $options->getTable(),
            $options->getTable(),
            $options->getJoinColumn(),
            $options->getIdColumn(),
        );

        $sql = vsprintf($sqlPattern, $values);

        $result = $this->adapter->query($sql, array());

        if (!$result->count()) {
            throw new \DomainException('No role loaded');
        }

        $roles = array();
        foreach ($result as $row) {
            if (isset($row->parent) && $row->parent) {
                $parentName = $row->parent;
            } else {
                $parentName = 0;
            }
            $roles[$parentName][] = $row->name;
        }
    }
}
