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
        $rootNode = $treeBuilder->root('dos_user');

        $this->addDefaults($rootNode, 'doctrine/orm', 'default', array(
            'user' => array('Default'),
            'user_group' => array('Default'),
        ));

        $rootNode
            ->append($this->createResourcesSection(array(
                'user' => array(
                    'model' => 'DoS\UserBundle\Model\User',
                    'repository' => 'DoS\UserBundle\Doctrine\ORM\UserRepository',
                    'controller' => 'DoS\UserBundle\Controller\UserController',
                    'form' => array(
                        'default' => 'DoS\UserBundle\Form\Type\UserType',
                    ),
                ),
                'user_group' => array(
                    'model' => 'DoS\UserBundle\Model\Group',
                    'form' => array(
                        'default' => 'DoS\UserBundle\Form\Type\GroupType',
                        'choice' => 'DoS\UserBundle\Form\Type\GroupChoiceType',
                    ),
                ),
                'user_oauth' => array(
                    'model' => 'DoS\UserBundle\Model\UserOAuth',
                ),
            )))
        ;

        return $treeBuilder;
    }
}
