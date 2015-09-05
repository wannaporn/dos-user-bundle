<?php

namespace DoS\UserBundle\Confirmation\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('token', 'hidden')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_verification';
    }
}
