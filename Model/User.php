<?php

namespace DoS\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\User\Model\User as BaseUser;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Toro\Bundle\CoreBundle\Model\MediaInterface;

class User extends BaseUser implements UserInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $displayname;

    /**
     * @var string
     */
    protected $confirmationType;

    /**
     * @var \DateTime
     */
    protected $confirmedAt;

    /**
     * @var ArrayCollection
     */
    protected $authorizationRoles;

    /**
     * @var MediaInterface
     */
    protected $picture;

    public function __construct()
    {
        parent::__construct();

        $this->authorizationRoles = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationRoles()
    {
        return $this->authorizationRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthorizationRole(RoleInterface $role)
    {
        if (!$this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthorizationRole(RoleInterface $role)
    {
        if ($this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->removeElement($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAuthorizationRole(RoleInterface $role)
    {
        return $this->authorizationRoles->contains($role);
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        $customer = $this->getCustomer();

        return $this->displayname
            ? $customer && trim($customer->getFullName())
                ? $customer->getFullName()
                : $this->username
            : $this->username;
    }

    /**
     * @param null|string $displayname
     */
    public function setDisplayName($displayname = null)
    {
        $this->displayname = $displayname;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string|void
     */
    public function getLang()
    {
        if ($this->locale) {
            if (preg_match('/_([a-z]{2})/i', $this->locale, $match)) {
                return strtolower($match[1]);
            }
        }

        return;
    }

    /**
     * @inheritdoc
     */
    public function getMediaPath()
    {
        return '/user';
    }

    /**
     * {@inheritdoc}
     */
    public function setPicture(MediaInterface $picture = null)
    {
        $this->picture = $picture;
    }

    /**
     * {@inheritdoc}
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfilePicture()
    {
        if ($this->picture) {
            return $this->picture->getMediaId();
        }

        foreach ($this->oauthAccounts as $account) {
            if ($avatar = $account->getProfilePicture()) {
                return $avatar;
            }
        }

        return;
    }

    /**
     * @inheritdoc
     */
    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    /**
     * @inheritdoc
     */
    public function setConfirmedAt(\DateTime $confirmedAt = null)
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @inheritdoc
     */
    public function confirmed(\DateTime $confirmedAt = null)
    {
        $this->setConfirmedAt($confirmedAt ?: new \DateTime());
        $this->setEnabled(true);
        $this->setConfirmationToken(null);
        $this->setPasswordRequestedAt(null);
    }

    /**
     * @inheritdoc
     */
    public function isConfirmed()
    {
        return $this->confirmedAt || $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (Boolean)$boolean;

        if (!$this->isConfirmed()) {
            $this->enabled = false;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationChannel($propertyPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($this, $propertyPath);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationRequestedAt()
    {
        return $this->getPasswordRequestedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationRequestedAt(\DateTime $dateTime = null)
    {
        $this->setPasswordRequestedAt($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationConfirmedAt()
    {
        return $this->getConfirmedAt();
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationConfirmedAt(\DateTime $dateTime = null)
    {
        $this->setConfirmedAt($dateTime);
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmationConfirmed()
    {
        return $this->isConfirmed();
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationRequest($token)
    {
        $this->setConfirmationToken($token);
        $this->setConfirmationRequestedAt(new \DateTime());
        $this->setEnabled(false);
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationConfirm()
    {
        $this->confirmed();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationType()
    {
        return $this->confirmationType;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationType($confirmationType)
    {
        $this->confirmationType = $confirmationType;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationDisableAccess()
    {
        $this->enabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmationEnableAccess()
    {
        $this->enabled = true;
    }
}
