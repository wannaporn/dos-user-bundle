<?php

namespace DoS\UserBundle\OAuth;

use Sylius\Component\User\Model\CustomerInterface;

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
     * {@inheritdoc}
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
}
