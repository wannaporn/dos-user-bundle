<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\OAuth\Security;
use FOS\UserBundle\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * User delete listener.
 */
class UserDeleteListener
{
    protected $security;
    protected $session;
    protected $router;
    protected $redirectTo;

    public function __construct(Security $security, UrlGeneratorInterface $router, SessionInterface $session, $redirectTo)
    {
        $this->security = $security;
        $this->session = $session;
        $this->router = $router;
        $this->redirectTo = $redirectTo;
    }

    public function deleteUser(ResourceEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user, UserInterface::class
            );
        }

        if ($this->security->getUsername() === $user->getUsernameCanonical()) {
            $event->stopPropagation();
            $this->session->getBag('flashes')->add('error', 'Cannot remove currently logged user.');
        }
    }
}
