<?php

namespace DoS\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // remove the username field
        $builder->remove('username');
    }

    public function getName()
    {
        return 'dos_user_registration';
    }
}
