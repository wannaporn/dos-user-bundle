<?php

namespace DoS\UserBundle\Confirmation;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface ConfirmationInterface
{
    const STORE_KEY = 'user_registration_confirmation_token';

    /**
     * @param array $options
     */
    public function resetOptions(array $options);

    /**
     * @param bool $valid
     */
    public function setValid($valid);

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    public function send(ConfirmationSubjectInterface $subject);

    /**
     * @param ConfirmationSubjectInterface $subject
     *
     * @return bool
     */
    public function canResend(ConfirmationSubjectInterface $subject);

    /**
     * @param Request $request
     * @param string $token
     *
     * @return FormInterface
     */
    public function verify(Request $request, $token);

    /**
     * @param Request $request
     *
     * @return FormInterface
     */
    public function resend(Request $request);

    /**
     * @param bool $clear
     *
     * @return string|void
     */
    public function getStoredToken($clear = false);

    /**
     * @return string
     *
     * @see http://php.net/manual/en/dateinterval.createfromdatestring.php
     */
    public function getTokenResendTimeAware();

    /**
     * @return string
     */
    public function getTargetChannel();

    /**
     * @return string
     */
    public function getObjectPath();

    /**
     * @param ConfirmationSubjectInterface $subject
     *
     * @return \DateTime|null
     */
    public function getTokenTimeAware(ConfirmationSubjectInterface $subject = null);

    /**
     * @param $token
     *
     * @return ConfirmationSubjectInterface
     */
    public function findSubjectWithToken($token);

    /**
     * @param $value
     *
     * @return ConfirmationSubjectInterface
     */
    public function findSubject($value);

    /**
     * @param ConfirmationSubjectInterface $subject
     * @return mixed
     */
    public function getSubjectValue(ConfirmationSubjectInterface $subject = null);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return FormInterface
     */
    public function createResendForm();

    /**
     * @return FormInterface
     */
    public function createVerifyForm();
}
