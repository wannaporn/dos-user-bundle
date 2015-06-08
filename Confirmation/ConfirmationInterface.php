<?php

namespace DoS\UserBundle\Confirmation;

interface ConfirmationInterface
{
    const STORE_KEY = 'user_registration_confirmation_token';

    /**
     * @param array $options
     */
    public function resetOptions(array $options);

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    public function send(ConfirmationSubjectInterface $subject);

    /**
     * @param string $token
     * @param array  $options
     *
     * @throws \Exception
     */
    public function verify($token, array $options = array());

    /**
     * @return string
     */
    public function getType();
}
