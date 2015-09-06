<?php

namespace DoS\UserBundle\Security;

use Sylius\Bundle\UserBundle\Security\UserLogin as BaseUserLogin;
use Sylius\Component\User\Model\UserInterface;

class UserLogin extends BaseUserLogin
{
    /**
     * {@inheritDoc}
     */
    public function login(UserInterface $user, $firewallName = 'main')
    {
        if (!$user->isEnabled()) {
            return;
        }

        parent::login($user, $firewallName);
    }
}
