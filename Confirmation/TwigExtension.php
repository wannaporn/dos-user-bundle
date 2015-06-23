<?php

namespace DoS\UserBundle\Confirmation;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormView;

class TwigExtension extends \Twig_Extension
{
    /**
     * @var ConfirmationFactory
     */
    protected $factory;

    /**
     * @var ConfirmationInterface
     */
    protected $service;

    public function __construct(ConfirmationFactory $factory)
    {
        $this->factory = $factory;
        $this->service = $factory->createActivedConfirmation(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ui_confirmation_type', array($this, 'getActivedType')),
            new \Twig_SimpleFunction('ui_confirmation_constraint', array($this, 'getConstraint')),
            new \Twig_SimpleFunction('ui_confirmation_target_path', array($this, 'getTargetChannel')),
        );
    }

    /**
     * @param FormView $formView
     *
     * @return null|FormError|FormErrorIterator
     */
    public function getConstraint(FormView $formView)
    {
        if ($this->service) {
            /** @var FormErrorIterator $errors */
            $errors = $formView->vars['errors'];

            return $this->service->getConstraint($errors->getForm());
        }

        return;
    }

    /**
     * @return null|string
     */
    public function getActivedType()
    {
        if ($this->service) {
            return $this->service->getType();
        }

        return;
    }

    /**
     * @return null|string
     */
    public function getTargetChannel()
    {
        if ($this->service) {
            return $this->service->getTargetChannel();
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ui_user_confirmation';
    }
}
