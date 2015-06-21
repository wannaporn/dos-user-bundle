<?php

namespace DoS\UserBundle\Confirmation;

use Doctrine\Common\Persistence\ObjectManager;
use DoS\UserBundle\Confirmation\Exception\InvalidTokenTimeException;
use DoS\UserBundle\Confirmation\Exception\NotFoundTokenSubjectException;
use Sylius\Component\Storage\StorageInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\Form\FormInterface;
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

    /**
     * @var bool
     */
    protected $isValid = true;

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

        $this->resetOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenSendTemplate()
    {
        return $this->options['token_send_template'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenVerifyTemplate()
    {
        return $this->options['token_verify_template'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenConfirmTemplate()
    {
        return $this->options['token_confirm_template'];
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmRoute()
    {
        return $this->options['routing_confirmation'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFailbackRoute()
    {
        return $this->options['routing_failback'];
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
        $subject->setConfirmationType($this->getType());
        $subject->confirmationRequest(
            $token = $this->tokenProvider->generateUniqueToken()
        );

        $this->sendToken($subject, $token);
        $this->storeSubject($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($token, array $options = array())
    {
        $subject = $this->findSubject($token);

        if (!$this->validateTimeAware($subject)) {
            throw new InvalidTokenTimeException();
        }

        try {
            $this->verifyToken($subject, $options);
        } catch (\Exception $e) {
            throw $e;
        }

        $subject->confirmationConfirm();

        $this->storage->removeData(self::STORE_KEY);
        $this->storeSubject($subject);

        return $subject;
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
    public function getConstraint(FormInterface $form)
    {
        $path = $this->getChannelObjectPath();

        foreach ($form->getErrors(true) as $error) {

            $class = get_class($error->getCause()->getConstraint());
            $classConstraint = 'Sylius\Bundle\UserBundle\Validator\Constraints\RegisteredUser';
            $classConfig = $this->options['channel_constraint_class'];

            if ($error->getOrigin()->getName() === $path
                && ($class === $classConfig || in_array($classConstraint, class_parents($class)))
            ) {
                $this->isValid = false;

                return $error;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getChannelObjectPath()
    {
        $paths = explode('.', $this->options['channel_path']);

        return $paths[count($paths) - 1];
    }

    /**
     * {@inheritdoc}
     */
    public function findSubject($token)
    {
        $er = $this->manager->getRepository($this->options['subject_class']);

        if (!$subject = $er->findOneBy(array($this->options['token_property_path'] => $token))) {
            throw new NotFoundTokenSubjectException();
        }

        return $subject;
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
    public function getTokenTimeAware(ConfirmationSubjectInterface $subject)
    {
        if (null === $timeAware = $this->options['token_time_aware']) {
            return null;
        }

        if (!$time = $subject->getConfirmationRequestedAt()) {
            return null;
        }

        $time->add(\DateInterval::createFromDateString($timeAware));

        return $time;
    }

    /**
     * @param ConfirmationSubjectInterface $subject
     */
    protected function storeSubject(ConfirmationSubjectInterface $subject)
    {
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

        return $time->getTimestamp() >= (new \DateTime())->getTimestamp();
    }

    /**
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                /*
                 * System user entity class.
                 */
                'subject_class' => 'DoS\UserBundle\Model\User',
                /*
                 * The user model property.
                 */
                'token_property_path' => 'confirmationToken',
                /*
                 * The sent template.
                 */
                'token_send_template' => null,
                /*
                 * The sent template.
                 */
                'token_confirm_template' => null,
                /*
                 * The verify template.
                 */
                'token_verify_template' => null,
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
                /*
                 * What's error constraint we used for catch exception.
                 */
                'channel_constraint_class' => 'Sylius\Bundle\UserBundle\Validator\Constraints\RegisteredUser',
                /**
                 * Using when not found any route.
                 */
                'routing_failback' => 'route_homepage',
                /**
                 * Using for redirection when duplicated registration.
                 */
                'routing_confirmation' => null,
            )
        );

        $resolver->setRequired(
            array(
                'subject_class',
                'token_property_path',
                'token_send_template',
                'token_verify_template',
                'token_confirm_template',
                'routing_confirmation',
            )
        );
    }

    /**
     * @param ConfirmationSubjectInterface $subject
     * @param string $token
     */
    abstract protected function sendToken(
        ConfirmationSubjectInterface $subject,
        $token
    );

    /**
     * @param ConfirmationSubjectInterface $subject
     * @param array $options
     *
     * @return true
     */
    abstract protected function verifyToken(
        ConfirmationSubjectInterface $subject,
        array $options = array()
    );

    /**
     * @return string
     */
    abstract public function getType();
}
