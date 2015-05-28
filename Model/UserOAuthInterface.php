<?php

namespace DoS\UserBundle\Model;

use Sylius\Component\User\Model\UserOAuthInterface as BaseUserOAuthInterface;

/**
 * User OAuth account interface.
 */
interface UserOAuthInterface extends BaseUserOAuthInterface
{
    /**
     * @return string
     */
    public function getProfilePicture();

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture);
}
