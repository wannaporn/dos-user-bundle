<?php

namespace DoS\UserBundle\Confirmation\OTP;

use DoS\UserBundle\Confirmation\ConfirmationAbstract;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use DoS\UserBundle\Confirmation\Exception\InvalidTokenVerifyException;
use DoS\UserBundle\Confirmation\Exception\NotFoundChannelException;
use DoS\UserBundle\Model\OneTimePasswordInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Confirmation extends ConfirmationAbstract
{
    protected function sendToken(ConfirmationSubjectInterface $subject, $token)
    {
        if (!$mobile = $subject->getConfirmationChannel($this->options['channel_path'])) {
            throw new NotFoundChannelException;
        }

        $this->sender->send(
            $this->options['token_send_template'],
            array($mobile),
            array('subject' => $subject, 'token' => $token)
        );

        $verify = $this->tokenProvider->generateUniqueToken();

        /** @var OneTimePasswordInterface $otp */
        $otp = new $this->options['otp_class'];
        $otp->setSubject($subject);
        $otp->setToken($token);
        $otp->setVerify($verify);

        $this->manager->persist($otp);
    }

    protected function verifyToken(ConfirmationSubjectInterface $subject, array $options = array())
    {
        if (empty($options['verifier'])) {
            throw new InvalidTokenVerifyException;
        }

        if (!$otp = $this->findOtp($subject)) {
            throw new InvalidTokenVerifyException;
        }

        if ($otp->getVerify() !== $options['verifier']) {
            throw new InvalidTokenVerifyException;
        }

        $otp->setConfirmed(true);
        $this->manager->persist($otp);

        return true;
    }

    /**
     * @param ConfirmationSubjectInterface $subject
     *
     * @return \DoS\UserBundle\Model\OneTimePasswordInterface
     */
    protected function findOtp(ConfirmationSubjectInterface $subject)
    {
        $er = $this->manager->getRepository($this->options['otp_class']);

        return $er->findOneBy(array(
            'subject' => $subject,
            'token' => $subject->getConfirmationToken()
        ));
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'otp_class' => 'DoS\UserBundle\Model\OneTimePassword',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'otp';
    }
}
