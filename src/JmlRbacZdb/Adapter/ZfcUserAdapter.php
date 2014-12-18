<?php
namespace JmlRbacZdb\Adapter;

use JmlRbacZdb\Mapper\RoleMapper;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;

class ZfcUserAdapter implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    const EVENT_NAME = 'find';
    const EVENT_PRIORITY = -5;

    protected $roleMapper;

    public function __construct(RoleMapper $roleMapper)
    {
        $this->setRoleMapper($roleMapper);
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(static::EVENT_NAME, [ $this, 'populateUserWithRoles' ], static::EVENT_PRIORITY);
    }

    public function populateUserWithRoles($e)
    {
        $this->getRoleMapper()->populateUserWithRoles($e->getParam('entity'));
    }

    protected function getRoleMapper()
    {
        return $this->roleMapper;
    }

    protected function setRoleMapper($roleMapper)
    {
        $this->roleMapper = $roleMapper;
    }
}
