<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

interface VerificationInterface
{
    /**
     * @return mixed
     */
    public function getVerifyValue();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return ConfirmationSubjectInterface
     */
    public function getSubject();

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    public function setSubject(ConfirmationSubjectInterface $subject = null);
}
