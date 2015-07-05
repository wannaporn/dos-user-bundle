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
                    'model' => $config['classes']['customer']['model'],
                    'controller' => $config['classes']['customer']['controller'],
                ),
                'user' => array(
                    'model' => $config['classes']['user']['model'],
                    'controller' => $config['classes']['user']['controller'],
                ),
                'user_oauth' => array(
                    'model' => $config['classes']['user_oauth']['model'],
                ),
                'group' => array(
                    'model' => $config['classes']['group']['model'],
                ),
            ),
        ));
    }
}
