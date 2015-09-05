<?php

namespace DoS\UserBundle\Confirmation;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface ConfirmationInterface
{
    const STORE_KEY = 'user_registration_confirmation_token';
    const REDIRECT_HEADER_KEY = 'x-user-registration-confirmation-redirect';

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
     * @param FormInterface $form
     *
     * @return null|FormError|FormErrorIterator
     */
    public function getConstraint(FormInterface $form);

    /**
     * @return string
     */
    public function getTokenSendTemplate();

    /**
     * @return string
     *
     * @see http://php.net/manual/en/dateinterval.createfromdatestring.php
     */
    public function getTokenResendTimeAware();

    /**
     * @return string
     * @deprecated
     */
    public function getTokenConfirmTemplate();

    /**
     * @return string
     * @deprecated
     */
    public function getTokenVerifyTemplate();

    /**
     * @return string
     */
    public function getConfirmationResendTemplate();

    /**
     * @return string
     */
    public function getTargetChannel();

    /**
     * @return string
     */
    public function getObjectPath();

    /**
     * @return string
     */
    public function getConfirmRoute();

    /**
     * @return string
     */
    public function getFailbackRoute();

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
