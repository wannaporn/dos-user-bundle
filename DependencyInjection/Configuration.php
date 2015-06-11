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
                'user' => array(
                    'model' => 'DoS\UserBundle\Model\User',
                    'interface' => 'DoS\UserBundle\Model\UserInterface',
                    'repository' => 'DoS\UserBundle\Doctrine\ORM\UserRepository',
                    'controller' => 'DoS\UserBundle\Controller\UserController',
                    'form' => array(
                        'default' => 'DoS\UserBundle\Form\Type\UserType',
                    ),
                ),
                'user_oauth' => array(
                    'model' => 'DoS\UserBundle\Model\UserOAuth',
                    'interface' => 'DoS\UserBundle\Model\UserOAuthInterface',
                ),
                'customer' => array(
                    'model' => 'DoS\UserBundle\Model\Customer',
                    'interface' => 'DoS\UserBundle\Model\CustomerInterface',
                ),
                'group' => array(
                    'model' => 'DoS\UserBundle\Model\Group',
                    'interface' => 'DoS\UserBundle\Model\GroupInterface',
                    'form' => array(
                        'default' => 'DoS\UserBundle\Form\Type\GroupType',
                        'choice' => 'DoS\UserBundle\Form\Type\GroupChoiceType',
                    ),
                ),
                'otp' => array(
                    'model' => 'DoS\UserBundle\Model\OneTimePassword',
                    'interface' => 'DoS\UserBundle\Model\OneTimePasswordInterface',
                ),
                'customer' => array(
                    'model' => 'DoS\UserBundle\Model\Customer',
                    'interface' => 'DoS\UserBundle\Model\CustomerInterface',
                ),
                'otp' => array(
                    'model' => 'DoS\UserBundle\Model\OneTimePassword',
                    'interface' => 'DoS\UserBundle\Model\OneTimePasswordInterface',
                ),
            ),
            'validation_groups' => array(
                'otp' => array('Default'),
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
                            /*->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('email')
                                    ->children()
                                        ->scalarNode('subject_class')->cannotBeEmpty()->end()
                                        ->scalarNode('xxx')->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()*/
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
