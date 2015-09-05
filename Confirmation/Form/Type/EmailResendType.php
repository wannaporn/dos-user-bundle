<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailResendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('email', 'email', array(
                'required' => true,
                'label' => 'ui.trans.user.confirmation_resend.form.email',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_resend_confirmation_email';
    }
}
