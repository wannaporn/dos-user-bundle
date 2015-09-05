<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use libphonenumber\PhoneNumber;

class OtpResend implements ResendInterface
{
    /**
     * @var ConfirmationSubjectInterface
     */
    protected $subject;

    /**
     * @var PhoneNumber
     */
    protected $mobile;

    /**
     * @return string
     */
    public function getSubjectValue()
    {
        return $this->mobile;
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
    public function setMobile(PhoneNumber $mobile)
    {
        $this->mobile = $mobile;
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
    public function setSubject(ConfirmationSubjectInterface $subject)
    {
        $this->subject = $subject;
    }
}
