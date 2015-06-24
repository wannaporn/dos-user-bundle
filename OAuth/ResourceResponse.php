<?php

namespace DoS\UserBundle\OAuth;

use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;

class ResourceResponse extends PathUserResponse
{
    /**
     * @param array|string $response
     */
    public function setResponse($response)
    {
        parent::setResponse($response);

        if (count($this->response)) {
            $keys = array_keys($this->response);
            $this->setPaths(array_combine($keys, $keys));
        }
    }

    /**
     * @param string     $path
     * @param null|mixed $default
     *
     * @return null|mixed
     */
    public function getPathValue($path, $default = null)
    {
        return $this->getValueForPath($path) ?: $default;
    }

    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->getPathValue('first_name');
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getPathValue('last_name');
    }

    /**
     * @return null|string
     */
    public function getGender()
    {
        return $this->getPathValue('gender');
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->getPathValue('locale');
    }
}
