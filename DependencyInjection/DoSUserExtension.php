<?php

namespace DoS\UserBundle\DependencyInjection;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class DoSUserExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getBundleConfiguration()
    {
        return new Configuration();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = parent::load($config, $container);

        $container->setParameter('dos.user.confirmation', $config['confirmation']);
        $container->setParameter('dos.user.confirmation.actived', $config['confirmation']['actived']);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $container->prependExtensionConfig('sylius_user', array(
            'resources' => array(
                'customer' => array(
                    'classes' => array(
                        'model' => 'DoS\UserBundle\Model\Customer',
                        'form' => array(
                            'default' => 'DoS\UserBundle\Form\Type\CustomerType',
                            'profile' => 'DoS\UserBundle\Form\Type\CustomerProfileType',
                        )
                    ),
                    'validation_groups' => array(
                        'default' => array('dos', 'sylius', 'sylius_customer_profile'),
                        'profile' => array('dos', 'sylius', 'sylius_customer_profile'),
                    )
                ),
                'user' => array(
                    'classes' => array(
                        'model' => 'DoS\UserBundle\Model\User',
                        'controller' => 'DoS\UserBundle\Controller\UserController',
                        'repository' => 'DoS\UserBundle\Doctrine\ORM\UserRepository',
                        'form' => array(
                            'default' => '\DoS\UserBundle\Form\Type\UserType',
                        )
                    ),
                    'validation_groups' => array(
                        'default' => array('dos', 'sylius'),
                        'registration' => array('dos', 'dos_registration', 'sylius', 'sylius_user_registration'),
                    )
                ),
                'user_oauth' => array(
                    'classes' => array(
                        'model' => 'DoS\UserBundle\Model\UserOAuth',
                    ),
                ),
            ),
            'validation_groups' => array(
                'customer_profile' => array('dos', 'sylius', 'sylius_customer_profile'),
                'customer_registration' => array('dos_registration', 'sylius', 'sylius_customer_profile', 'sylius_user_registration'),
                'user_registration' => array('dos', 'dos_registration', 'sylius', 'sylius_user_registration'),
            )
        ));
    }
}
