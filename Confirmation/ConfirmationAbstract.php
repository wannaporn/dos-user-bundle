<?php

namespace DoS\UserBundle\Confirmation;

use Doctrine\Common\Persistence\ObjectManager;
use DoS\UserBundle\Confirmation\Exception\InvalidTokenResendTimeException;
use DoS\UserBundle\Confirmation\Model\ResendInterface;
use DoS\UserBundle\Confirmation\Model\VerificationInterface;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ConfirmationAbstract implements ConfirmationInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ConfirmationSubjectFinderInterface
     */
    protected $finder;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var TokenProviderInterface
     */
    protected $tokenProvider;

    /**
     * @var SenderInterface
     */
    protected $sender;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var bool
     */
    protected $isValid = true;

    public function __construct(
        ObjectManager $manager,
        ConfirmationSubjectFinderInterface $finder,
        SenderInterface $sender,
        StorageInterface $storage,
        TokenProviderInterface $tokenProvider,
        FormFactoryInterface $formFactory,
        array $options = array()
    ) {
        $this->manager = $manager;
        $this->finder = $finder;
        $this->storage = $storage;
        $this->tokenProvider = $tokenProvider;
        $this->sender = $sender;
        $this->formFactory = $formFactory;

        $this->resetOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenResendTimeAware()
    {
        return $this->options['token_resend_time_aware'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetChannel()
    {
        return $this->options['channel_path'];
    }

    /**
     * {@inheritdoc}
     */
    public function resetOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    public function setValid($valid)
    {
        $this->isValid = $valid;
    }

    /**
     * {@inheritdoc}
     */
    public function send(ConfirmationSubjectInterface $subject)
    {
        $subject->confirmationDisableAccess();
        $subject->setConfirmationType($this->getType());
        $subject->confirmationRequest(
            $token = $this->tokenProvider->generateUniqueToken()
        );

        $this->sendToken($subject, $token);

        if (!$this->isValid) {
            return;
        }

        $this->manager->persist($subject);
        $this->manager->flush();

        $this->storage->setData(self::STORE_KEY, $subject->getConfirmationToken());
    }

    /**
     * {@inheritdoc}
     */
    public function resend(Request $request)
    {
        $form = $this->createResendForm();
        $data = $form->getData();

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH'))
            && $form->handleRequest($request)->isValid()) {
            try {
                $this->canResend($data->getSubject());
                $this->send($data->getSubject());
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $form;
    }

    protected function successVerify(ConfirmationSubjectInterface $subject)
    {
        $subject->confirmationConfirm();
        $subject->confirmationEnableAccess();

        $this->storage->removeData(self::STORE_KEY);
        $this->manager->persist($subject);
        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoredToken($clear = false)
    {
        if ($this->storage->hasData(self::STORE_KEY)) {
            $token = $this->storage->getData(self::STORE_KEY);

            if ($clear) {
                $this->storage->removeData(self::STORE_KEY);
            }

            return $token;
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectPath()
    {
        $paths = explode('.', $this->options['channel_path']);

        return $paths[count($paths) - 1];
    }

    /**
     * {@inheritdoc}
     */
    public function findSubjectWithToken($token)
    {
        if (empty($token)) {
            return null;
        }

        try {
            $subject = $this->finder->findConfirmationSubject($this->options['token_property_path'], $token);
        } catch (\Exception $e) {
            $subject = null;
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function findSubject($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            $subject = $this->finder->findConfirmationSubject($this->options['channel_path'], $value);
        } catch (\Exception $e) {
            $subject = null;
        }

        return $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectValue(ConfirmationSubjectInterface $subject = null)
    {
        if (!$subject) {
            return;
        }

        return $subject->getConfirmationChannel($this->options['channel_path']);
    }

    /**
     * @param ConfirmationSubjectInterface $subject
     *
     * @return bool
     */
    protected function validateTimeAware(ConfirmationSubjectInterface $subject)
    {
        if (null === $time = $this->getTokenTimeAware($subject)) {
            return true;
        }

        return $time->getTimestamp() > (new \DateTime())->getTimestamp();
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenTimeAware(ConfirmationSubjectInterface $subject = null)
    {
        if (null === $subject || null === $timeAware = $this->options['token_time_aware']) {
            return;
        }

        if (!$time = $subject->getConfirmationRequestedAt()) {
            return;
        }

        $time->add(\DateInterval::createFromDateString($timeAware));

        return $time;
    }

    /**
     * {@inheritdoc}
     */
    public function canResend(ConfirmationSubjectInterface $subject)
    {
        if ($subject->isConfirmationConfirmed()) {
            return false;
        }

        if (null === $timeAware = $this->options['token_resend_time_aware']) {
            return true;
        }

        if (!$time = $subject->getConfirmationRequestedAt()) {
            return false;
        }

        $time->add(\DateInterval::createFromDateString($timeAware));

        $valid = $time->getTimestamp() >= (new \DateTime())->getTimestamp();

        if (false === $valid) {
            $exception = new InvalidTokenResendTimeException();
            $exception->setTime($time);
            $exception->setTimeAware($timeAware);

            throw $exception;
        }

        return $valid;
    }

    public function createResendForm()
    {
        return $this->formFactory->create(
            $this->options['token_resend_form'],
            $this->getResendModel()
        );
    }

    public function createVerifyForm()
    {
        return $this->formFactory->create(
            $this->options['token_verify_form'],
            $this->getVerifyModel()
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                /*
                 * The user model property.
                 */
                'token_property_path' => 'confirmationToken',
                /*
                 * The resend form.
                 */
                'token_resend_form' => null,
                /*
                 * The verify form.
                 */
                'token_verify_form' => null,
                /*
                 * Life time of valid token.
                 * using \DateInterval::createFromDateString()
                 * @link http://php.net/manual/en/dateinterval.createfromdatestring.php
                 */
                'token_time_aware' => null,
                'token_resend_time_aware' => null,
                /*
                 * Which the prpoerty path to using as channel.
                 */
                'channel_path' => 'customer.email',
            )
        );

        $resolver->setRequired(
            array(
                'token_property_path',
                'token_resend_form',
                'token_verify_form',
            )
        );
    }

    /**
     * @param ConfirmationSubjectInterface $subject
     * @param string                       $token
     */
    abstract protected function sendToken(
        ConfirmationSubjectInterface $subject,
        $token
    );

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return ResendInterface
     */
    abstract public function getResendModel();

    /**
     * @return VerificationInterface
     */
    abstract public function getVerifyModel();
}
