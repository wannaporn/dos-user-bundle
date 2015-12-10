<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomerType extends BaseCustomerType
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
