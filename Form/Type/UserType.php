<?php

namespace DoS\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends ProfileFormType
{
    /** @var string */
    private $dataClass;

    /**
     * {@inheritdoc}
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'ui.trans.user.form.firstname',
            ))
            ->add('lastName', 'text', array(
                'label' => 'ui.trans.user.form.lastname',
            ))
        ;

        $this->buildUserForm($builder, $options);

        $builder
            ->add('plainPassword', 'password', array(
                'label' => 'ui.trans.user.form.password',
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'ui.trans.user.form.enabled',
            ))
            ->add('groups', 'dos_user_group_choice', array(
                'label' => 'ui.trans.user.form.groups',
                'multiple' => true,
                'required' => false,
            ))
            ->add('authorizationRoles', 'sylius_role_choice', array(
                'label' => 'sylius.form.user.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
            ->remove('username')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                $groups = array('Profile', 'Default');
                if ($data && !$data->getId()) {
                    $groups[] = 'ProfileAdd';
                }

                return $groups;
            },
            'cascade_validation' => true,
            'intention' => 'profile',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_user';
    }
}
