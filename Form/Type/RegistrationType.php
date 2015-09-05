<?php

namespace DoS\UserBundle\Form\Type;

use DoS\UserBundle\Form\EventListener\ConfirmationFormListener;
use Sylius\Bundle\UserBundle\Form\Type\CustomerRegistrationType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
