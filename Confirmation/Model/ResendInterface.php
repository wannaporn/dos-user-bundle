<?php
namespace DoS\UserBundle\Confirmation\Model;

use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;

interface ResendInterface
{
    /**
     * @return mixed
     */
    public function getSubjectValue();

    /**
     * @return ConfirmationSubjectInterface
     */
    public function getSubject();

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    public function setSubject(ConfirmationSubjectInterface $subject = null);
}
