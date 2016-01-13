<?php

namespace DoS\UserBundle\Model;

use DoS\CernelBundle\Model\MediaPathAwareInterface;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;
use Sylius\Component\Media\Model\ImageInterface;

/**
 * User interface.
 */
interface UserInterface extends BaseUserInterface, MediaPathAwareInterface, ConfirmationSubjectInterface, IdentityInterface
{
    /**
     * @return bool
     */
    public function isLocked();

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

    /**
     * @param ImageInterface|null $picture
     */
    public function setPicture(ImageInterface $picture = null);

    /**
     * @return ImageInterface
     */
    public function getPicture();

    /**
     * Resize security roles with sylius role map.
     */
    public function resizeSecurityRoles();
}
