<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends BaseUserType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password.label',
                'required' => false,
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled',
                'required' => false,
            ))
        ;

        $builder
            ->add('locale', 'locale', array(
                'label' => 'dos.form.user.locale',
                'required' => false
            ))

            ->add('displayname', 'text', array(
                'label' => 'dos.form.user.displayname',
                'required' => false
            ))

            ->add('authorizationRoles', 'sylius_role_choice', array(
                'label' => 'sylius.form.user.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ))
        ;
    }
}
