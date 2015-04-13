<?php

namespace DoS\UserBundle;

use DoS\ResourceBundle\DependencyInjection\AbstractResourceBundle;

class DoSUserBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'DoS\UserBundle\Model\UserInterface' => 'dos.model.user.class',
            'DoS\UserBundle\Model\UserOAuthInterface' => 'dos.model.user_oauth.class',
            'DoS\UserBundle\Model\GroupInterface' => 'dos.model.user_group.class',
        );
    }
}
