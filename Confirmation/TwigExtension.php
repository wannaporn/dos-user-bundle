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

    public function __construct(ConfirmationFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ui_confirmation_constraint', array($this, 'getConstraint')),
        );
    }

    /**
     * @param FormView $formView
     *
     * @return null|FormError|FormErrorIterator
     */
    public function getConstraint(FormView $formView)
    {
        if ($actived = $this->factory->createActivedConfirmation(false)) {
            /** @var FormErrorIterator $errors */
            $errors = $formView->vars['errors'];

            return $actived->getConstraint($errors->getForm());
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
