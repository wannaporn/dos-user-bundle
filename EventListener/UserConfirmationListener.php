<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
use DoS\UserBundle\Model\CustomerInterface;
use DoS\UserBundle\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

class UserConfirmationListener
{
    /**
     * @var ConfirmationFactory
     */
    protected $factory;

    public function __construct(ConfirmationFactory $factory)
    {
        $this->factory = $factory;
    }

    public function disableUser(GenericEvent $event)
    {
        /** @var CustomerInterface $subject */
        $subject = $event->getSubject();
        $user = $subject->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, UserInterface::class);
        }

        $user->setEnabled(false);
    }

    public function confirmUser(GenericEvent $event)
    {
        /** @var CustomerInterface $subject */
        $subject = $event->getSubject();
        $user = $subject->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, UserInterface::class);
        }

        if ($confirmation = $this->factory->createActivedConfirmation(false)) {
            $confirmation->send($user);
        }
    }
}
