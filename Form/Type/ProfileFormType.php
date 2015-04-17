<?php

namespace DoS\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Profile form.
 */
class ProfileFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array(
                'label' => 'ui.trans.profile.form.avatar',
                'required' => false,
            ))
            ->add('firstName', 'text', array(
                'label' => 'ui.trans.profile.form.first_name',
                'required' => true,
            ))
            ->add('lastName', 'text', array(
                'label' => 'ui.trans.profile.form.last_name',
                'required' => true,
            ))
            ->add('email', 'email', array(
                'label' => 'ui.trans.profile.form.email',
                'required' => true,
            ))
            ->add('mobile', 'tel', array(
                'label' => 'ui.trans.profile.form.mobile',
                'required' => false,
                'default_region' => 'TH',
            ))
        ;
    }

    public function getName()
    {
        return 'dos_user_profile';
    }
}
