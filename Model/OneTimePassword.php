<?php

namespace DoS\UserBundle\Model;

use DoS\ResourceBundle\Model\TimestampableInterface;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

class OneTimePassword implements OneTimePasswordInterface
{
    /**
     * @var ConfirmationSubjectInterface
     */
    protected $subject;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $verify;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var bool
     */
    protected $confirmed;

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(ConfirmationSubjectInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getVerify()
    {
        return $this->verify;
    }

    /**
     * {@inheritdoc}
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
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
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }
}
