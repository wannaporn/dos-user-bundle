<?php

namespace DoS\UserBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use DoS\UserBundle\Security\Security;
use DoS\UserBundle\Model\UserUpdaterAwareInterface;
use DoS\UserBundle\Model\UserInterface;
use Sylius\Component\User\Model\UserAwareInterface;

class UserAwareResourceListener
{
    /**
     * @var Security
     */
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
            if (!$entity instanceof UserAwareInterface && !$entity instanceof UserUpdaterAwareInterface) {
                continue;
            }

            if ($entity instanceof UserAwareInterface && !$entity->getUser()) {
                $entity->setUser($this->getUser());
            }

            if ($entity instanceof UserUpdaterAwareInterface && !$entity->getUpdater()) {
                $entity->setUpdater($this->getUser());
            }

            $entityManager->persist($entity);
            $metadata = $entityManager->getClassMetadata(get_class($entity));
            $unitOfWork->recomputeSingleEntityChangeSet($metadata, $entity);
        }
    }

    /**
     * @return UserInterface|null
     */
    private function getUser()
    {
        return $this->security->getUser();
    }
}
