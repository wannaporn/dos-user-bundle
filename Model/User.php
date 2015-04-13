<?php

namespace DoS\UserBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use DoS\CoreBundle\Model\AddressInterface;
use FOS\UserBundle\Model\User as BaseUser;
use libphonenumber\PhoneNumber;
use Sylius\Component\Rbac\Model\RoleInterface;

class User extends BaseUser implements UserInterface
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $mobile;

    /**
     * @var string
     */
    protected $gender;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * @var ArrayCollection|AddressInterface[]
     */
    protected $addresses;

    /**
     * @var ArrayCollection
     */
    protected $oauthAccounts;

    /**
     * @var ArrayCollection
     */
    protected $authorizationRoles;

    /**
     * @var string
     */
    protected $displayname;

    /**
     * @var string
     */
    protected $fullname;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $path;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->addresses = new ArrayCollection();
        $this->oauthAccounts = new ArrayCollection();
        $this->authorizationRoles = new ArrayCollection();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        if (null !== $billingAddress && !$this->hasAddress($billingAddress)) {
            $this->addAddress($billingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        if (null !== $shippingAddress && !$this->hasAddress($shippingAddress)) {
            $this->addAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function addAddress(AddressInterface $address)
    {
        if (!$this->hasAddress($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->addresses->removeElement($address);
        $address->setUser(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddress(AddressInterface $address)
    {
        return $this->addresses->contains($address);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        $this->setFullName();
        $this->setDisplayName();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        $this->setFullName();
        $this->setDisplayName();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        parent::setEmail($email);

        if (!$this->getUsername()) {
            parent::setUsername($email);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);

        if (!$this->getUsername()) {
            parent::setUsernameCanonical($emailCanonical);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthAccounts()
    {
        return $this->oauthAccounts;
    }

    /**
     * {@inheritdoc}
     */
    public function getOAuthAccount($provider)
    {
        if ($this->oauthAccounts->isEmpty()) {
            return;
        }

        $filtered = $this->oauthAccounts->filter(function ($oauth) use ($provider) {
            /* @var $oauth UserOAuthInterface */

            return $provider === $oauth->getProvider();
        });

        if ($filtered->isEmpty()) {
            return;
        }

        return $filtered->current();
    }

    /**
     * {@inheritdoc}
     */
    public function addOAuthAccount(UserOAuthInterface $oauth)
    {
        if (!$this->oauthAccounts->contains($oauth)) {
            $this->oauthAccounts->add($oauth);
            $oauth->setUser($this);
        }

        return $this;
    }

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
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = parent::getRoles();

        foreach ($this->getAuthorizationRoles() as $role) {
            $roles = array_merge($roles, $role->getSecurityRoles());
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getFullName()
    {
        return $this->fullname;
    }

    /**
     * @deprecated
     */
    public function getLabel()
    {
        return $this->displayname;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayname;
    }

    /**
     * @param null|string $fullname
     */
    public function setFullName($fullname = null)
    {
        $this->fullname = $fullname ?: trim(sprintf('%s %s', $this->firstName, $this->lastName));
    }

    /**
     * @param null|string $displayname
     */
    public function setDisplayName($displayname = null)
    {
        $this->displayname = $displayname ?: $this->fullname;
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
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
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

        foreach($this->oauthAccounts as $account) {
            if ($avatar = $account->getProfilePicture()) {
                return $avatar;
            }
        }

        return null;
    }
}
