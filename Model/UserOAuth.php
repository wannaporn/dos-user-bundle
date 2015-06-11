<?php

namespace DoS\UserBundle\Model;

use Sylius\Component\User\Model\UserOAuth as BaseUserOAuth;

/**
 * User OAuth model.
 */
class UserOAuth extends BaseUserOAuth implements UserOAuthInterface
{
    /**
     * @var string
     */
    protected $profilePicture;

    /**
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }
}
