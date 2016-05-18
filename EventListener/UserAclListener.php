<?php

namespace DoS\UserBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use DoS\UserBundle\Model\UserAclOwnerAwareInterface;

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
            if (!$entity instanceof UserAclOwnerAwareInterface) {
                continue;
            }

            if (!$owner = $entity->getAclOwner()) {
                throw new \RuntimeException("Not found AclOwner for: " . get_class($entity));
            }

            $this->container->get('oneup_acl.manager')
                ->addObjectPermission($entity, MaskBuilder::MASK_OWNER, $owner)
            ;

            $entityManager->persist($entity);
            $metadata = $entityManager->getClassMetadata(get_class($entity));
            $unitOfWork->recomputeSingleEntityChangeSet($metadata, $entity);
        }
    }
}
