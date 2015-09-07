<?php

namespace DoS\UserBundle\OAuth;

use DoS\UserBundle\Model\CustomerInterface;
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
        return $this->getPathValue('firstname');
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->getPathValue('lastname');
    }

    /**
     * @return null|string
     */
    public function getBirthday()
    {
        if ($birthday = $this->getPathValue('birthday')) {
            // YYYY-MM-DD
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $birthday)) {
                return \DateTime::createFromFormat('Y-m-d', $birthday);
            }

            throw new \LogicException('Unkonw birthday format.');
        }

        return $birthday;
    }

    /**
     * @return null|string
     */
    public function getGender()
    {
        $gender = CustomerInterface::UNKNOWN_GENDER;

        switch($this->getPathValue('gender')) {
            case 'male':
                $gender = CustomerInterface::MALE_GENDER;
                break;

            case 'female':
                $gender = CustomerInterface::FEMALE_GENDER;
                break;
        }

        return $gender;
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->getPathValue('locale');
    }
}
