<?php

namespace DoS\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerRegistrationType;

class RegistrationType extends CustomerRegistrationType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_registration';
    }
}
