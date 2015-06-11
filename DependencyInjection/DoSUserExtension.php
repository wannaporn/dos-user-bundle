<?php

namespace DoS\UserBundle\DependencyInjection;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoSUserExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        list($config) = $this->configure($config, new Configuration(), $container,
            self::CONFIGURE_LOADER |
            self::CONFIGURE_DATABASE |
            self::CONFIGURE_PARAMETERS |
            self::CONFIGURE_VALIDATORS |
            self::CONFIGURE_FORMS
        );

        $container->setParameter('dos.user.confirmation', $config['confirmation']);
        $container->setParameter('dos.user.confirmation.actived', $config['confirmation']['actived']);
    }
}
