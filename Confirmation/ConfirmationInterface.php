<?php

namespace DoS\UserBundle\Confirmation;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;

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
     * @param string $token
     * @param array $options
     *
     * @throws \Exception
     */
    public function verify($token, array $options = array());

    /**
     * @param bool $clear
     *
     * @return string|void
     */
    public function getStoredToken($clear = false);

    /**
     * @param FormInterface $form
     * @return null|FormError|FormErrorIterator
     */
    public function getConstraint(FormInterface $form);

    /**
     * @return string
     */
    public function getTokenSendTemplate();

    /**
     * @return string
     */
    public function getTokenConfirmTemplate();

    /**
     * @return string
     */
    public function getTokenVerifyTemplate();

    /**
     * @return string
     */
    public function getConfirmRoute();

    /**
     * @return string
     */
    public function getFailbackRoute();

    /**
     * @return string
     */
    public function getTokenTimeAware();

    /**
     * @param $token
     *
     * @return ConfirmationSubjectInterface
     */
    public function findSubject($token);

    /**
     * @return string
     */
    public function getType();
}
