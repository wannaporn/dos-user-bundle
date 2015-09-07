<?php

namespace DoS\UserBundle\OAuth;

/**
 * @author liverbool <phaiboon@intbizth.com>
 * @see https://developers.facebook.com/docs/graph-api/reference/user
 */
class FacebookResponse extends ResourceResponse
{
    /**
     * {@inheritdoc}
     */
    public function getProfilePicture()
    {
        return sprintf('https://graph.facebook.com/v2.3/%s/picture?width=400',
            $this->getPathValue('id')
        );
    }

    /**
     * @return null|string
     */
    public function getBirthday()
    {
        if ($birthday = $this->getPathValue('birthday')) {
            return \DateTime::createFromFormat('m/d/Y', $birthday);
        }
    }
}
