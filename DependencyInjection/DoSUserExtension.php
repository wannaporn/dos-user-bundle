<?php

namespace DoS\UserBundle\DependencyInjection;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class DoSUserExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure($config, new Configuration(), $container,
            self::CONFIGURE_LOADER |
            self::CONFIGURE_DATABASE |
            self::CONFIGURE_PARAMETERS |
            self::CONFIGURE_VALIDATORS |
            self::CONFIGURE_FORMS
        );

        $container->setParameter('dos.user.confirmation', $config['confirmation']);
        $container->setParameter('dos.user.confirmation.actived', $config['confirmation']['actived']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('sylius_user', array(
            'classes' => array(
                'customer' => array(
                    'model' => 'DoS\UserBundle\Model\Customer',
                    'form' => array(
                        'default' => 'DoS\UserBundle\Form\Type\CustomerType',
                        'profile' => 'DoS\UserBundle\Form\Type\CustomerProfileType',
                    )
                ),
                'user' => array(
                    'model' => 'DoS\UserBundle\Model\User',
                    'controller' => 'DoS\UserBundle\Controller\UserController',
                    'repository' => 'DoS\UserBundle\Doctrine\ORM\UserRepository',
                    'form' => array(
                        'default' => '\DoS\UserBundle\Form\Type\UserType',
                    )
                ),
                'user_oauth' => array(
                    'model' => 'DoS\UserBundle\Model\UserOAuth',
                ),
            ),
            'validation_groups' => array(
                'customer' => array('dos', 'sylius', 'sylius_customer_profile'),
                'customer_profile' => array('dos', 'sylius', 'sylius_customer_profile'),
                'customer_registration' => array('dos_registration', 'sylius', 'sylius_customer_profile', 'sylius_user_registration'),
                'user' => array('dos', 'sylius'),
                'user_registration' => array('dos', 'dos_registration', 'sylius', 'sylius_user_registration'),
            )
        ));
    }
}
