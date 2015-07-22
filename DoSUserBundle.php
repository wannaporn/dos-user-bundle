<?php

namespace DoS\UserBundle;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceBundle;
use DoS\UserBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DoSUserBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\SyliusOverridePass());
        $container->addCompilerPass(new Compiler\ConfirmationPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getModelInterfaces()
    {
        return array(
            'DoS\UserBundle\Model\UserInterface' => 'dos.model.user.class',
            'DoS\UserBundle\Model\CustomerInterface' => 'dos.model.customer.class',
            'DoS\UserBundle\Model\GroupInterface' => 'dos.model.group.class',
            'DoS\UserBundle\Model\UserOAuthInterface' => 'dos.model.user_oauth.class',
            'DoS\UserBundle\Model\OneTimePasswordInterface' => 'dos.model.otp.class',
        );
    }
}
