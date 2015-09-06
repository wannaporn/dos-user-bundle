<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use DoS\UserBundle\Confirmation\Email\Confirmation;
use DoS\UserBundle\Confirmation\Form\EventListener\SubjectAdderListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VerificationType extends AbstractType
{
    /**
     * @var Confirmation
     */
    protected $confirmation;

    public function __construct(Confirmation $confirmation)
    {
        $this->confirmation = $confirmation;
    }

    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder->addEventSubscriber(new SubjectAdderListener($this->confirmation));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_verification';
    }
}
