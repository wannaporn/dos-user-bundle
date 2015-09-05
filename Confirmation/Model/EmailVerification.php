<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

class EmailVerification implements VerificationInterface
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
     * {@inheritdoc}
     */
    public function getVerifyValue()
    {
        return $this->token;
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
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(ConfirmationSubjectInterface $subject = null)
    {
        $this->subject = $subject;
    }
}
