<?php

namespace DoS\UserBundle\Form\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationFactory;
use DoS\UserBundle\Model\CustomerInterface;
use DoS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;

class ConfirmationFormListener implements EventSubscriberInterface
{
    /**
     * @var ConfirmationFactory
     */
    protected $factory;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(ConfirmationFactory $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'isRegisteredUser',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function isRegisteredUser(FormEvent $event)
    {
        if (!$confirmation = $this->factory->createActivedConfirmation(false)) {
            return;
        }

        if (!$error = $confirmation->getConstraint($form = $event->getForm())) {
            return;
        }

        /** @var CustomerInterface $customer */
        $customer = $event->getData();
        /** @var UserInterface $user */
        $user = $customer->getUser();

        if ($user->isConfirmationConfirmed()) {
            return;
        }

        $confirmation->send($user);

        $this->requestStack->getCurrentRequest()->headers->set(
            $confirmation::REDIRECT_HEADER_KEY,
            $confirmation->getConfirmRoute()
        );
    }
}
