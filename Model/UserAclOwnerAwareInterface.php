<?php

namespace DoS\UserBundle\Model;

interface UserAclOwnerAwareInterface
{
    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getAclOwner();
}
