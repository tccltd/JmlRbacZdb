<?php

namespace JmlRbacZdbTest\Role;

use JmlRbacZdb\Role\ZendDbRoleProvider;
use JmlRbacZdbTest\Bootstrap;
use JmlRbacZdbTest\RbacServiceTestCase;
use ZfcRbac\Service\Rbac as RbacService;

class ZendDbRoleProviderTest extends RbacServiceTestCase
{
    /**
     * @var ZendDbRoleProvider
     */
    protected $roleProvider;

    public function setUp()
    {
        $adapter = Bootstrap::getServiceManager()->get('JmlRbacZdbTest\Adapter');
        $options = [
            'table' => 'role',
            'idColumn' => 'id',
            'nameColumn' => 'name',
            'joinColumn' => 'parent_id',
        ];
        $this->roleProvider = new ZendDbRoleProvider($adapter, $options);
    }

    /**
     * Test roles loaded from Db
     * @return boolean
     */
    public function testRoles()
    {
        $roleProvider = $this->roleProvider;
        $roles = $roleProvider->getRoles(['admin']);
        $this->assertCount(1, $roles);
        $roles = $roleProvider->getRoles(['admin', 'member']);
        $this->assertCount(2, $roles);
    }

    public function testMissingRole()
    {
        $roleProvider = $this->roleProvider;
        $this->setExpectedException(
            'DomainException',
            ZendDbRoleProvider::ERROR_MISSING_ROLE
        );
        $roleProvider->getRoles(['admin', 'member', 'unknown']);
    }
}
