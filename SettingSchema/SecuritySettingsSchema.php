<?php

namespace DoS\UserBundle\SettingSchema;

use DoS\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Security settings schema.
 */
class SecuritySettingsSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array(
                'enabled' => false,
            ))
            ->setAllowedTypes(array(
                'enabled' => array('bool'),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.settings.security.enabled',
                'required' => false,
            ))
        ;
    }
}
