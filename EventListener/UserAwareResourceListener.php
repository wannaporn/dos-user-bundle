<?php

namespace DoS\UserBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use DoS\UserBundle\Security\Security;
use DoS\UserBundle\Model\UserUpdaterAwareInterface;
use DoS\UserBundle\Model\UserAwareInterface;
use DoS\UserBundle\Model\UserInterface;

class UserAwareResourceListener implements EventSubscriber
{
    /**
     * @var Security
     */
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $object = $event->getObject();

        if ($object instanceof UserAwareInterface && !$object->getUser()) {
            $object->setUser($this->getUser());
            // init updater
            $this->preUpdate($event);
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        $object = $event->getObject();

        if ($object instanceof UserUpdaterAwareInterface && !$object->getUpdater()) {
            $object->setUpdater($this->getUser());
        }
    }

    /**
     * @return UserInterface|null
     */
    private function getUser()
    {
        return $this->security->getUser();
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }
}
