<?php

namespace DoS\UserBundle\Model;

use libphonenumber\PhoneNumber;
use Sylius\Component\User\Model\CustomerInterface as BaseCustomerInterface;

interface CustomerInterface extends BaseCustomerInterface
{
    /**
     * @return PhoneNumber
     */
    public function getMobile();

    /**
     * @param PhoneNumber $mobile
     */
    public function setMobile(PhoneNumber $mobile = null);
}
