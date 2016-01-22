<?php

namespace DoS\UserBundle\Model;

use DoS\ResourceBundle\Model\TimestampableInterface;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface OneTimePasswordInterface extends TimestampableInterface, ResourceInterface
{
    /**
     * @return ConfirmationSubjectInterface
     */
    public function getSubject();

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    public function setSubject(ConfirmationSubjectInterface $subject);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getVerify();

    /**
     * @param string $verify
     */
    public function setVerify($verify);

    /**
     * @param bool $confirmed
     */
    public function setConfirmed($confirmed);

    /**
     * @return bool
     */
    public function isConfirmed();
}
