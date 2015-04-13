<?php

namespace DoS\UserBundle\Model;

/**
 * User OAuth account interface.
 */
interface UserOAuthInterface extends UserAwareInterface
{
    /**
     * Get OAuth provider name.
     *
     * @return string
     */
    public function getProvider();

    /**
     * Set OAuth provider name.
     *
     * @param string $provider
     *
     * @return self
     */
    public function setProvider($provider);

    /**
     * Get OAuth identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Set OAuth identifier.
     *
     * @param string $identifier
     *
     * @return self
     */
    public function setIdentifier($identifier);

    /**
     * Get OAuth access token.
     *
     * @return string
     */
    public function getAccessToken();

    /**
     * Set OAuth access token.
     *
     * @param string $accessToken
     *
     * @return self
     */
    public function setAccessToken($accessToken);

    /**
     * @return string
     */
    public function getProfilePicture();

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture);
}
