<?php

namespace DoS\UserBundle;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceBundle;
use DoS\UserBundle\DependencyInjection\Compiler;

class DoSUserBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\SyliusOverridePass());
    }
}
