<?php

namespace DoS\UserBundle\OAuth;

class GoogleResponse extends ResourceResponse
{
    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->getPathValue('given_name');
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getPathValue('family_name');
    }
}
