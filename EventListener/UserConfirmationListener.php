<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
use DoS\UserBundle\Model\CustomerInterface;
use DoS\UserBundle\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class UserConfirmationListener
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
     * @return ConfirmationFactory
     */
    protected function getFactory()
    {
        return $this->container->get('dos.user.confirmation.factory');
    }

    /**
     * @return RouterInterface
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }

    public function confirmRequest(GenericEvent $event)
    {
        /** @var CustomerInterface $subject */
        $subject = $event->getSubject();
        $user = $subject->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, UserInterface::class);
        }

        if ($confirmation = $this->getFactory()->createActivedConfirmation()) {
            $confirmation->send($user);
        }
    }

    public function redirectToConfirmation(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $confirmation = $this->getFactory()->createActivedConfirmation();
        $headers = $event->getRequest()->headers;

        if ($confirmation && $key = $headers->get($confirmation::REDIRECT_HEADER_KEY)) {
            $headers->remove($confirmation::REDIRECT_HEADER_KEY);

            $event->setResponse(
                new RedirectResponse($this->getRouter()->generate($key))
            );
        }
    }
}
