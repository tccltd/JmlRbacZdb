<?php
/**
 * @author jmleroux
 */

namespace JmlRbacZdb\Factory;

use JmlRbacZdb\Role\ZendDbRoleProvider;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ZendDbRoleProviderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = isset($spec['connection']) ? $spec['connection'] : null;
        if (!$adapter) {
            throw new \DomainException('Missing required parameter: connection');
        }

        if (is_string($adapter) && $serviceLocator->has($adapter)) {
            $adapter = $serviceLocator->get($adapter);
        } else {
            throw new \DomainException('Failed to find Db Connection');
        }

        $options = isset($spec['options']) ? (array)$spec['options'] : array();

        return new ZendDbRoleProvider($adapter, $options);
    }
}
