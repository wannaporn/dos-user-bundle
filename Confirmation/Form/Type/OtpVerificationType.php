<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use DoS\UserBundle\Confirmation\Form\EventListener\SubjectAdderListener;
use DoS\UserBundle\Confirmation\OTP\Confirmation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OtpVerificationType extends AbstractType
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
        $builder
            ->add('token', 'hidden')
            ->add('otp', 'text')
        ;

        $builder->addEventSubscriber(new SubjectAdderListener($this->confirmation));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_verification_otp';
    }
}
