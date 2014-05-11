<?php
/**
 * @author jmleroux
 */

namespace JmlRbacZdb\Factory;

use JmlRbacZdb\Role\ZendDbRoleProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcRbac\Role\RoleProviderPluginManager;

class ZendDbRoleProviderFactory implements FactoryInterface, MutableCreationOptionsInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param RoleProviderPluginManager $pluginManager
     * @return ZendDbRoleProvider|mixed
     * @throws \DomainException
     */
    public function createService(ServiceLocatorInterface $pluginManager)
    {
        $adapter = isset($this->options['connection']) ? $this->options['connection'] : null;
        if (!$adapter) {
            throw new \DomainException('Missing required parameter: connection');
        }
        $serviceManager = $pluginManager->getServiceLocator();
        $adapter = $serviceManager->get($adapter);
        $providerOptions = isset($this->options['options']) ? (array)$this->options['options'] : array();
        return new ZendDbRoleProvider($adapter, $providerOptions);
    }

    /**
     * Set creation options
     *
     * @param  array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }
}
