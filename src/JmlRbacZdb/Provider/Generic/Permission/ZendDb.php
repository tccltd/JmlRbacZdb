<?php
namespace JmlRbacZdb\Provider\Generic\Permission;

use Zend\Db\Adapter\Adapter;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Provider\AbstractProvider;
use ZfcRbac\Provider\Event;

class ZendDb extends AbstractProvider
{
    /**
     * @var Adapter
     */
    protected $_adapter;

    /**
     * @var array
     */
    protected $_roles;

    /**
     * @var ZendDbOptions
     */
    protected $_options;

    public function __construct(Adapter $adapter, array $options)
    {
        $this->_adapter = $adapter;
        $this->_options = new ZendDbOptions($options);
    }

    public function attach(EventManagerInterface $events)
    {
        $events->attach(Event::EVENT_LOAD_PERMISSIONS, array($this, 'loadPermissions'));
    }

    public function detach(EventManagerInterface $events)
    {
        $events->detach($this);
    }

    public function loadPermissions(Event $e)
    {
        $options = $this->_options;

        $sqlPattern = 'SELECT p.%s AS permission, r.%s AS role
            FROM %s p
            LEFT JOIN %s rp ON rp.%s = p.%s
            LEFT JOIN %s r ON rp.%s = r.%s';

        $values = array(
            $options->getPermissionNameColumn(),
            $options->getRoleNameColumn(),
            $options->getPermissionTable(),
            $options->getRoleJoinTable(),
            $options->getPermissionJoinColumn(),
            $options->getPermissionIdColumn(),
            $options->getRoleTable(),
            $options->getRoleJoinColumn(),
            $options->getRoleIdColumn(),
        );

        $sql = vsprintf($sqlPattern, $values);

        $result = $this->_adapter->query($sql, array());

        if (!$result->count()) {
            throw new \DomainException('No permission loaded');
        }

        $rbac = $e->getRbac();

        foreach ($result as $row) {
            if ($rbac->hasRole($row->role)) {
                $rbac->getRole($row->role)->addPermission($row->permission);
            }
        }
    }

    public static function factory(ServiceLocatorInterface $sl, array $spec)
    {
        $adapter = isset($spec['connection']) ? $spec['connection'] : null;
        if (!$adapter) {
            throw new \DomainException('Missing required parameter: connection');
        }

        if (!is_string($adapter) || $sl->has($adapter)) {
            $adapter = $sl->get($adapter);
        } else {
            throw new \DomainException('Failed to find Db Connection');
        }

        $options = isset($spec['options']) ? (array)$spec['options'] : array();

        return new ZendDb($adapter, $options);
    }
}
