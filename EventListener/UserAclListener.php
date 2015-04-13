<?php

namespace DoS\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use DoS\UserBundle\Model\UserAclOwnerAwareInterface;
use DoS\UserBundle\Model\UserAwareInterface;

class UserAclListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        if (!$this->container->has('problematic.acl_manager')) {
            return;
        }

        $object = $args->getObject();

        // explicitly
        if ($object instanceof UserAclOwnerAwareInterface && $object->getAclOwner()) {
            $this->container->get('problematic.acl_manager')
                ->addObjectPermission($object, MaskBuilder::MASK_OWNER, $object->getAclOwner())
            ;
        }

        // default
        if ($object instanceof UserAwareInterface
            && !$object instanceof UserAclOwnerAwareInterface
            && $object->getUser()) {
            $this->container->get('problematic.acl_manager')
                ->addObjectPermission($object, MaskBuilder::MASK_OWNER, $object->getUser())
            ;
        }
    }
}
