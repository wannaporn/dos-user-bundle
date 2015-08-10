<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType as BaseCustomerProfileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CustomerProfileType extends BaseCustomerProfileType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('email') // use multiple emails strategy like github.
            ->add('user', 'dos_user_avatar')

            ->add('mobile', 'tel', array(
                'label' => 'ui.trans.customer.form.mobile',
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
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'phone_default_region' => 'TH',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_customer_profile';
    }
}
