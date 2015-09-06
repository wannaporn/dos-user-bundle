<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use DoS\UserBundle\Confirmation\Form\EventListener\SubjectAdderListener;
use DoS\UserBundle\Confirmation\OTP\Confirmation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OtpResendType extends AbstractType
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
            ->add('mobile', 'tel', array(
                'required' => true,
                'error_bubbling' => true,
                'label' => 'ui.trans.user.confirmation_resend.form.mobile',
            ))
        ;

        $builder->addEventSubscriber(new SubjectAdderListener($this->confirmation));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_resend_confirmation_otp';
    }
}
