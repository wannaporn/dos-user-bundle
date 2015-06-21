<?php

namespace DoS\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SyliusOverridePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $class = $container->getParameter('dos.listener.default_username.class');

        $container->getDefinition('sylius.listener.default_username')
            ->setClass($class)
        ;

        $container->setParameter('sylius.listener.default_username.class', $class);

        $container->getDefinition('validator.unique.registered_user')
            ->setClass('DoS\UserBundle\Validator\Constraints\RegisteredUserValidator')
        ;
    }
}
