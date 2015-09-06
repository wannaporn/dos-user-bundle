<?php

namespace DoS\UserBundle\Confirmation\Form\EventListener;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
use DoS\UserBundle\Confirmation\Model\ResendInterface;
use DoS\UserBundle\Confirmation\Model\VerificationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class SubjectAdderListener implements EventSubscriberInterface
{
    /**
     * @var ConfirmationInterface
     */
    protected $confirmation;

    public function __construct(ConfirmationInterface $confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'addSubject'
        );
    }

    public function addSubject(FormEvent $event)
    {
        $data = $event->getData();
        $subject = null;

        if ($data instanceof ResendInterface) {
            $subject = $this->confirmation->findSubject($data->getSubjectValue());
        }

        if ($data instanceof VerificationInterface) {
            $subject = $this->confirmation->findSubjectWithToken($data->getToken());
        }

        $data->setSubject($subject);
    }
}
