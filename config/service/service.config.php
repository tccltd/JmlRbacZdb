<?php
use JmlRbacZdb\Adapter\ZfcUserAdapter;
use JmlRbacZdb\Hydrator\RoleHydrator;
use JmlRbacZdb\Mapper\RoleMapper;
use Rbac\Role\HierarchicalRole;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Sql;

return [
    'factories' => [
        'jmlrbaczdb_roleMapper' => function ($sm) {
            return new RoleMapper(
                new Sql($sm->get('Zend\Db\Adapter\Adapter')),
                new HydratingResultSet(new RoleHydrator(), new HierarchicalRole(''))
            );
        },
        'jmlrbaczdb_zfcUserAdapter' => function ($sm) {
            return new ZfcUserAdapter($sm->get('jmlrbaczdb_roleMapper'));
        },
    ],
];
