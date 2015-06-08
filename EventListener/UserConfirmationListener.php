<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
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

    public function confirmUser(GenericEvent $event)
    {
        /** @var UserInterface $subject */
        $subject = $event->getSubject();

        if ($subject instanceof UserInterface) {
            throw new UnexpectedTypeException($subject, UserInterface::class);
        }

        if ($confirmation = $this->factory->createActivedConfirmation(false)) {
            $confirmation->send($subject);
        }
    }
}
