<?php

namespace DoS\UserBundle\EventListener;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Reload user when their profile updated.
 */
class UserUpdateListener
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function processUser(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user, UserInterface::class
            );
        }

        $this->userManager->updateUser($user);
        $this->userManager->reloadUser($user);
    }
}
