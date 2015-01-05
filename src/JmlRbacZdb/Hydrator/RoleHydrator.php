<?php
namespace JmlRbacZdb\Hydrator;

use Zend\Stdlib\Hydrator\AbstractHydrator;

class RoleHydrator extends AbstractHydrator
{
    // TODO: Multiple permissions?
    public function hydrate(array $data, $object)
    {
        $object->__construct($data['role_name']);
        if (isset($data['permission_name'])) $object->addPermission($data['permission_name']);

        return $object;
    }

    // TODO: Cannot extract permissions.
    public function extract($object)
    {
        return [
            'role_name' => $object->getName(),
        ];
    }
}
