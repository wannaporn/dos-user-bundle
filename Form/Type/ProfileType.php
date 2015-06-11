<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends CustomerProfileType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array(
                'label' => 'ui.trans.profile.form.avatar',
                'required' => false,
            ))
            ->add('mobile', 'tel', array(
                'label' => 'ui.trans.profile.form.mobile',
                'required' => false,
                'default_region' => $options['phone_default_region'],
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'phone_default_region' => 'TH',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_profile';
    }
}
