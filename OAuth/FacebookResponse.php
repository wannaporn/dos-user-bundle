<?php

namespace DoS\UserBundle\OAuth;

class FacebookResponse extends ResourceResponse
{
    /**
     * @return string|null
     */
    public function getProfilePicture()
    {
        return sprintf('https://graph.facebook.com/v2.3/%s/picture?width=400',
            $this->getPathValue('id')
        );
    }
}
