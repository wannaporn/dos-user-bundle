<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType as BaseCustomerProfileType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerProfileType extends BaseCustomerProfileType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('mobile', 'tel', array(
                'label' => 'ui.trans.customer.form.mobile',
                'required' => false,
                'default_region' => 'TH',
            ))
        ;
    }
}
