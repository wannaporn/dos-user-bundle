<?php

namespace DoS\UserBundle\Model;

use DoS\ResourceBundle\Model\ImageInterface;
use libphonenumber\PhoneNumber;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * User interface.
 */
interface UserInterface extends BaseUserInterface, ImageInterface
{
    /**
     * @return PhoneNumber
     */
    public function getMobile();

    /**
     * @param PhoneNumber $mobile
     */
    public function setMobile(PhoneNumber $mobile = null);

    /**
     * @return string
     */
    public function getDisplayName();

    /**
     * @param $displayname
     */
    public function setDisplayName($displayname);

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @param string $locale
     */
    public function setLocale($locale);

    /**
     * @return null|string
     */
    public function getProfilePicture();

    /**
     * @param \DateTime|null $confirmedAt
     *
     * @return mixed
     */
    public function confirmed(\DateTime $confirmedAt = null);

    /**
     * @return bool
     */
    public function isConfirmed();

    /**
     * @return \DateTime
     */
    public function getConfirmedAt();

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(\DateTime $confirmedAt = null);
}
