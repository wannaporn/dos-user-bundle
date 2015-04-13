<?php

namespace DoS\UserBundle\Model;

/**
 * User aware interface.
 */
interface UserAwareInterface
{
    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();

    /**
     * Set user.
     *
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);
}
