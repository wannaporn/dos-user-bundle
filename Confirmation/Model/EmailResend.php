<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

class EmailResend implements ResendInterface
{
    /**
     * @var ConfirmationSubjectInterface
     */
    protected $subject;

    /**
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getSubjectValue()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
