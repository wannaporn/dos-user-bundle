<?php

namespace DoS\UserBundle\Model;

use libphonenumber\PhoneNumber;
use Sylius\Component\User\Model\User as BaseUser;

class User extends BaseUser implements UserInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var PhoneNumber
     */
    protected $mobile;

    /**
     * @var string
     */
    protected $displayname;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var \DateTime
     */
    protected $confirmedAt;

    /**
     /**
     * @return PhoneNumber
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param PhoneNumber $mobile
     */
    public function setMobile(PhoneNumber $mobile = null)
    {
        $this->mobile = $mobile;
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
            : $this->username
        ;
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
     * {@inheritdoc}
     */
    public function hasFile()
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath()
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProfilePicture()
    {
        if ($this->path) {
            return $this->path;
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
}
