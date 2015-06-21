<?php

namespace DoS\UserBundle\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
use DoS\UserBundle\Model\CustomerInterface;
use DoS\UserBundle\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class UserConfirmationListener
{
    /**
     * @var ConfirmationFactory
     */
    protected $factory;

    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(ConfirmationFactory $factory, RouterInterface $router)
    {
        $this->factory = $factory;
        $this->router = $router;
    }

    public function confirmRequest(GenericEvent $event)
    {
        /** @var CustomerInterface $subject */
        $subject = $event->getSubject();
        $user = $subject->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, UserInterface::class);
        }

        if ($confirmation = $this->factory->createActivedConfirmation()) {
            $confirmation->send($user);
        }
    }

    public function redirectToConfirmation(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $confirmation = $this->factory->createActivedConfirmation();
        $headers = $event->getRequest()->headers;

        if ($confirmation && $key = $headers->get($confirmation::REDIRECT_HEADER_KEY)) {
            $headers->remove($confirmation::REDIRECT_HEADER_KEY);

            $event->setResponse(
                new RedirectResponse($this->router->generate($key))
            );
        }
    }
}
