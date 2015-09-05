<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

class OtpVerification implements VerificationInterface
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
    protected $otp;

    /**
     * {@inheritdoc}
     */
    public function getVerifyValue()
    {
        return $this->otp;
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

    /**
     * @return string
     */
    public function getOtp()
    {
        return $this->otp;
    }

    /**
     * @param string $otp
     */
    public function setOtp($otp)
    {
        $this->otp = $otp;
    }
}
