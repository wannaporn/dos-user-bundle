<?php

namespace Sylius\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;

class UserAvatarType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'file', array(
                'label' => 'ui.trans.user.form.avatar',
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dos_user_avatar';
    }
}
