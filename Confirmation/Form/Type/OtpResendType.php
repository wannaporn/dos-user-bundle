<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OtpResendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('mobile', 'tel', array(
                'required' => true,
                'label' => 'ui.trans.user.confirmation_resend.form.mobile',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_resend_confirmation_otp';
    }
}
