<?php

namespace Dos\UserBundle\Model;

use Doctrine\Common\Collections\Collection;
use Dos\CoreBundle\Model\AddressInterface;
use Dos\CoreBundle\Model\ImageInterface;
use FOS\UserBundle\Model\UserInterface as BaseUserInterface;
use libphonenumber\PhoneNumber;
use Sylius\Component\Rbac\Model\IdentityInterface;

/**
 * User interface.
 */
interface UserInterface extends BaseUserInterface, ImageInterface, IdentityInterface
{
    /**
     * @return \DateTime
     */
    public function getLastLogin();

    /**
     * Get first name.
     *
     * @return string
     */
    public function getFirstName();

    /**
     * Set first name.
     *
     * @param string $firstName
     */
    public function setFirstName($firstName);

    /**
     * Get last name.
     *
     * @return string
     */
    public function getLastName();

    /**
     * Set last name.
     *
     * @param string $lastName
     */
    public function setLastName($lastName);

    /**
     * Get currency.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency.
     *
     * @param string $currency
     */
    public function setCurrency($currency);

    /**
     * Get billing address.
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address.
     *
     * @param AddressInterface $billingAddress
     */
    public function setBillingAddress(AddressInterface $billingAddress = null);

    /**
     * Get shipping address.
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address.
     *
     * @param AddressInterface $shippingAddress
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null);

    /**
     * Get addresses.
     *
     * @return Collection|AddressInterface[]
     */
    public function getAddresses();

    /**
     * Add address.
     *
     * @param AddressInterface $address
     */
    public function addAddress(AddressInterface $address);

    /**
     * Remove address.
     *
     * @param AddressInterface $address
     */
    public function removeAddress(AddressInterface $address);

    /**
     * Has address?
     *
     * @param AddressInterface $address
     *
     * @return bool
     */
    public function hasAddress(AddressInterface $address);

    /**
     * Get connected OAuth accounts.
     *
     * @return Collection|UserOAuthInterface[]
     */
    public function getOAuthAccounts();

    /**
     * Get connected OAuth account.
     *
     * @param string $provider
     *
     * @return null|UserOAuthInterface
     */
    public function getOAuthAccount($provider);

    /**
     * Connect OAuth account.
     *
     * @param UserOAuthInterface $oauth
     *
     * @return self
     */
    public function addOAuthAccount(UserOAuthInterface $oauth);

    /**
     * @return PhoneNumber
     */
    public function getMobile();

    /**
     * @param PhoneNumber $mobile
     */
    public function setMobile(PhoneNumber $mobile = null);

    /**
     * @inheritdoc
     */
    public function getFullName();

    /**
     * @deprecated
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param $displayname
     */
    public function setDisplayName($displayname);

    /**
     * @param $fullname
     */
    public function setFullName($fullname);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return string
     */
    public function getGender();

    /**
     * @param string $gender
     */
    public function setGender($gender);

    /**
     * @return null|string
     */
    public function getProfilePicture();
}
