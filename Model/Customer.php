<?php

namespace DoS\UserBundle\Model;

use libphonenumber\PhoneNumber;
use Sylius\Component\User\Model\Customer as BaseCustomer;

class Customer extends BaseCustomer implements CustomerInterface
{
    /**
     * @var PhoneNumber
     */
    protected $mobile;

    /**
     * {@inheritdoc}
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * {@inheritdoc}
     */
    public function setMobile(PhoneNumber $mobile = null)
    {
        $this->mobile = $mobile;
    }
}
