<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
use DoS\UserBundle\Model\CustomerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent as ResourceEvent;

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

    /**
     * @param ResourceEvent $event
     */
    public function sendConfirmation(ResourceEvent $event)
    {
        $customer = $event->getSubject();

        if (null === $customer || !$customer instanceof CustomerInterface) {
            return;
        }

        if (!$user = $customer->getUser()) {
            return;
        }

        if ($confirmation = $this->factory->createActivedConfirmation()) {
            $confirmation->send($user);
        }
    }
}
