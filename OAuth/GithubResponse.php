<?php

namespace DoS\UserBundle\OAuth;

class GithubResponse extends ResourceResponse
{
    /**
     * @return string|null
     */
    public function getProfilePicture()
    {
        return $this->getPathValue('avatar_url');
    }
}
