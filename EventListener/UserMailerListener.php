<?php

namespace DoS\UserBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * Generic user mailer listener.
 */
class UserMailerListener
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param FilterUserResponseEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendUserConfirmationEmail(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user, 'Sylius\Component\Core\Model\UserInterface'
            );
        }

        if (!$user->isEnabled()) {
            return;
        }

        $this->emailSender->send('user_confirmation', array($user->getEmail()), array('user' => $user));
    }
}
