<?php

namespace DoS\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ConfirmationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('dos.user.confirmation.factory')) {
            return;
        }

        $definition = $container->findDefinition('dos.user.confirmation.factory');
        $taggedServices = $container->findTaggedServiceIds('dos.user.confirmation');
        $defaultOptions = $container->getParameter('dos.user.confirmation');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $def = new Reference($id);
                $alias = $attributes['alias'];
                $definition->addMethodCall('add', array($def));

                if (array_key_exists($alias, $defaultOptions['types'])) {
                    $container
                        ->findDefinition($id)
                        ->addMethodCall('resetOptions', array($defaultOptions['types'][$alias]))
                    ;
                }
            }
        }

        if ($container->hasParameter('dos.user.confirmation.actived')) {
            $definition->addMethodCall(
                'setActivedService',
                array($container->getParameter('dos.user.confirmation.actived'))
            );
        }
    }
}
