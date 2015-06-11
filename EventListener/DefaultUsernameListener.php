<?php

namespace DoS\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

class DefaultUsernameListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof UserInterface) {
            return;
        }

        $customer = $item->getCustomer();

        if (null !== $customer && null === $item->getUsername()) {
            $item->setUsername($customer->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(LifecycleEventArgs $event)
    {
        $item = $event->getEntity();

        if (!$item instanceof CustomerInterface) {
            return;
        }

        $user = $item->getUser();

        if (null !== $user && $user->getUsername() === null) {
            $user->setUsername($item->getEmail());
            $entityManager = $event->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush($user);
        }
    }
}
