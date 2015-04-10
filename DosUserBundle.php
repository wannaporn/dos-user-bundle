<?php

namespace Dos\UserBundle;

use Dos\ResourceBundle\DependencyInjection\AbstractResourceBundle;

class DosUserBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Dos\UserBundle\Model\UserInterface' => 'dos.model.user.class',
            'Dos\UserBundle\Model\UserOAuthInterface' => 'dos.model.user_oauth.class',
            'Dos\UserBundle\Model\GroupInterface' => 'dos.model.user_group.class',
        );
    }
}
