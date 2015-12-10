<?php

namespace DoS\UserBundle\DependencyInjection;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends AbstractResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $this->setDefaults($node = $treeBuilder->root('dos_user'), array(
            'classes' => array(
                'otp' => array(
                    'model' => 'DoS\UserBundle\Model\OneTimePassword',
                    'interface' => 'DoS\UserBundle\Model\OneTimePasswordInterface',
                ),
            ),
            'validation_groups' => array(
                'otp' => array('dos'),
            ),
        ));

        $node
            ->children()
                ->arrayNode('confirmation')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('actived')
                            ->defaultNull()
                            ->cannotBeEmpty()
                        ->end()

                        ->arrayNode('types')
                            ->useAttributeAsKey('name')
                            ->prototype('variable')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
