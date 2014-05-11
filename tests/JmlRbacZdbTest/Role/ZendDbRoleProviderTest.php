<?php

namespace JmlRbacZdbTest\Role;

use JmlRbacZdb\Role\ZendDbRoleProvider;
use JmlRbacZdbTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class ZendDbRoleProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ZendDbRoleProvider
     */
    protected $roleProvider;

    public function setUp()
    {
        $adapter = Bootstrap::getServiceManager()->get('JmlRbacZdbTest\Adapter');
        $options = [
            'role_table' => 'role',
            'role_name_column' => 'name',
            'role_join_column' => 'role',
            'permission_table' => 'permission',
            'permission_name_column' => 'name',
            'permission_join_column' => 'permission',
        ];
        $this->roleProvider = new ZendDbRoleProvider($adapter, $options);
    }

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

    public function testPermissions()
    {
        $roleProvider = $this->roleProvider;
        $roles = $roleProvider->getRoles(['admin']);
        $adminRole = $roles[0];
        $this->assertTrue($adminRole->hasPermission('read'));
        $this->assertTrue($adminRole->hasPermission('edit'));
        $this->assertTrue($adminRole->hasPermission('delete'));
        $roles = $roleProvider->getRoles(['member']);
        $memberRole = $roles[0];
        $this->assertTrue($memberRole->hasPermission('read'));
        $this->assertFalse($memberRole->hasPermission('edit'));
        $this->assertFalse($memberRole->hasPermission('delete'));
    }
}
