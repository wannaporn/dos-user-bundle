<?php

namespace DoS\UserBundle\Form\Type;

use DoS\UserBundle\Form\EventListener\ConfirmationFormListener;
use Sylius\Bundle\UserBundle\Form\Type\CustomerRegistrationType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends CustomerRegistrationType
{
    /**
     * @var ConfirmationFormListener
     */
    protected $confirmationListener;

    public function __construct(
        $dataClass,
        array $validationGroups = array(),
        RepositoryInterface $customerRepository,
        ConfirmationFormListener $confirmationListener
    )
    {
        parent::__construct($dataClass, $validationGroups, $customerRepository);

        $this->confirmationListener = $confirmationListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventSubscriber($this->confirmationListener);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_user_registration';
    }
}
