<?php

namespace DoS\UserBundle\Confirmation;

use Doctrine\Common\Persistence\ObjectManager;
use DoS\UserBundle\Confirmation\Exception\InvalidTokenTimeException;
use DoS\UserBundle\Confirmation\Exception\NotFoundTokenSubjectException;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ConfirmationAbstract implements ConfirmationInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

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
     * @var array
     */
    protected $options = array();

    public function __construct(
        ObjectManager $manager,
        SenderInterface $sender,
        StorageInterface $storage,
        TokenProviderInterface $tokenProvider,
        array $options = array()
    ) {
        $this->manager = $manager;
        $this->storage = $storage;
        $this->tokenProvider = $tokenProvider;
        $this->sender = $sender;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function send(ConfirmationSubjectInterface $subject)
    {
        $subject->confirmationRequest(
            $token = $this->tokenProvider->generateUniqueToken()
        );

        $this->sendToken($subject, $token);
        $this->storeSubject($subject);
        $this->storage->setData(self::STORE_KEY, $token);
    }

    public function verify($token, array $options = array())
    {
        $subject = $this->findTokenSubject($token);

        if (!$this->validateTimeAware($subject)) {
            throw new InvalidTokenTimeException();
        }

        try {
            $this->verifyToken($subject, $options);
        } catch (\Exception $e) {
            throw $e;
        }

        $this->storage->removeData(self::STORE_KEY);
        $this->storeSubject($subject);
    }

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
     * @param $token
     *
     * @return ConfirmationSubjectInterface
     */
    protected function findTokenSubject($token)
    {
        $er = $this->manager->getRepository($this->options['subject_class']);

        if (!$subject = $er->findOneBy(array($this->options['token_property_path'] => $token))) {
            throw new NotFoundTokenSubjectException();
        }

        return $subject;
    }

    protected function validateTimeAware(ConfirmationSubjectInterface $subject)
    {
        if (null === $timeAware = $this->options['token_time_aware']) {
            return true;
        }

        if (!$time = $subject->getConfirmationRequestedAt()) {
            return false;
        }

        $time->add(\DateInterval::createFromDateString($timeAware));

        return $time->getTimestamp() > (new \DateTime())->getTimestamp();
    }

    protected function storeSubject(ConfirmationSubjectInterface $subject)
    {
        $this->manager->persist($subject);
        $this->manager->flush();
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            /*
             * System user entity class.
             */
            'subject_class' => 'DoS\UserBundle\Model\User',
            /*
             * The user model property.
             */
            'token_property_path' => 'confirmationToken',
            /*
             * The user model property.
             */
            'token_send_template' => null,
            /*
             * Life time of valid token.
             * using \DateInterval::createFromDateString()
             * @link http://php.net/manual/en/dateinterval.createfromdatestring.php
             */
            'token_time_aware' => null,
            /*
             * Which the prpoerty path to using as channel.
             */
            'channel_path' => 'customer.email',
        ));

        $resolver->setRequired(array(
            'subject_class',
            'token_property_path',
            'token_send_template',
        ));
    }

    abstract protected function sendToken(
        ConfirmationSubjectInterface $subject,
        $token
    );

    abstract protected function verifyToken(
        ConfirmationSubjectInterface $subject,
        array $options = array()
    );
}
