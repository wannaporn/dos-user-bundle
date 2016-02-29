<?php

namespace DoS\UserBundle\EventListener;

use Sylius\Component\User\Model\CustomerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

class DefaultUsernameListener
{
    /**
     * @param OnFlushEventArgs $onFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        $entityManager = $onFlushEventArgs->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!$entity instanceof CustomerInterface) {
                continue;
            }

            $user = $entity->getUser();

            if (null !== $user && $user->getUsername() === null) {
                $user->setUsername($entity->getEmail());
                $user->setUsernameCanonical($user->getEmail());
                $entityManager->persist($user);
                $userMetadata = $entityManager->getClassMetadata(get_class($user));
                $unitOfWork->recomputeSingleEntityChangeSet($userMetadata, $user);
            }
        }
    }
}
