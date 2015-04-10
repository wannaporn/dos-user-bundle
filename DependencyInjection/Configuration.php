<?php

namespace Dos\UserBundle\DependencyInjection;

use Dos\ResourceBundle\DependencyInjection\AbstractResourceConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends AbstractResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dos_user');

        $this->addDefaults($rootNode, 'doctrine/orm', 'default', array(
            'user' => array('Default'),
            'user_group' => array('Default'),
        ));

        $rootNode
            ->append($this->createResourcesSection(array(
                'user' => array(
                    'model' => 'Dos\UserBundle\Model\User',
                    'repository' => 'Dos\UserBundle\Doctrine\ORM\UserRepository',
                    'controller' => 'Dos\UserBundle\Controller\UserController',
                    'form' => array(
                        'default' => 'Dos\UserBundle\Form\Type\UserType',
                    ),
                ),
                'user_group' => array(
                    'model' => 'Dos\UserBundle\Model\Group',
                    'form' => array(
                        'default' => 'Dos\UserBundle\Form\Type\GroupType',
                        'choice' => 'Dos\UserBundle\Form\Type\GroupChoiceType',
                    ),
                ),
                'user_oauth' => array(
                    'model' => 'Dos\UserBundle\Model\UserOAuth',
                ),
            )))
        ;

        return $treeBuilder;
    }
}
