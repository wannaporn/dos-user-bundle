<?php

namespace DoS\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
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
        return $this->displayname;
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
}
