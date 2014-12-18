<?php
use JmlRbacZdb\Adapter\ZfcUserAdapter;
use JmlRbacZdb\Hydrator\RoleHydrator;
use JmlRbacZdb\Mapper\RoleMapper;
use Rbac\Role\HierarchicalRole;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;

return [
    'factories' => [
        'jmlrbacdb_role_mapper' => function ($sm) {
            return new RoleMapper(
                new Sql($sm->get('Zend\Db\Adapter\Adapter')),
                new HydratingResultSet(new RoleHydrator(), new HierarchicalRole(''))
            );
        },
        'jmlrbacdb_zfcuser_adapter' => function ($sm) {
            return new ZfcUserAdapter($sm->get('jmlrbacdb_role_mapper'));
        },
    ],
];
