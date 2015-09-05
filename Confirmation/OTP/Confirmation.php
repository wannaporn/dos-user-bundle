<?php

namespace DoS\UserBundle\Confirmation\OTP;

use DoS\UserBundle\Confirmation\ConfirmationAbstract;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use DoS\UserBundle\Confirmation\Exception\NotFoundChannelException;
use DoS\UserBundle\Confirmation\Model\OtpResend;
use DoS\UserBundle\Confirmation\Model\OtpVerification;
use DoS\UserBundle\Confirmation\Model\VerificationInterface;
use DoS\UserBundle\Model\OneTimePasswordInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Confirmation extends ConfirmationAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function sendToken(ConfirmationSubjectInterface $subject, $token)
    {
        if (!$mobile = $subject->getConfirmationChannel($this->options['channel_path'])) {
            throw new NotFoundChannelException();
        }

        $verify = $this->tokenProvider->generateUniqueToken();

        $this->sender->send(
            $this->options['token_send_template'],
            array($mobile),
            array('subject' => $subject, 'token' => $token, 'verify' => $verify)
        );

        /** @var OneTimePasswordInterface $otp */
        $otp = new $this->options['otp_class']();
        $otp->setSubject($subject);
        $otp->setToken($token);
        $otp->setVerify($verify);

        $this->manager->persist($otp);
    }

    /**
     * {@inheritdoc}
     */
    public function verify(Request $request, $token)
    {
        $form = $this->createVerifyForm();

        /** @var VerificationInterface $data */
        $data = $form->getData();
        $data->setToken($token);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH'))) {
            $form->submit($request, !$request->isMethod('PATCH'));

            $data->setSubject($this->findSubjectWithToken($data->getToken()));

            if (!$form->isValid()) {
                return $form;
            }

            if (!$this->validateTimeAware($subject = $data->getSubject())) {
                $form->addError(new FormError('ui.trans.user.confirmation.verify.invalid_time'));
            }

            if (!$otp = $this->findOtp($data->getSubject())) {
                $form->addError(new FormError('ui.trans.user.confirmation.verify.invalid_token'));
            }

            if ($otp->getVerify() !== $data->getVerifyValue()) {
                $form->addError(new FormError('ui.trans.user.confirmation.verify.invalid_otp'));
            }

            if (!$form->getErrors(true)->count()) {
                $otp->setConfirmed(true);
                $this->successVerify($subject);
            }
        }

        return $form;
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
            'token' => $subject->getConfirmationToken(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'otp_class' => 'DoS\UserBundle\Model\OneTimePassword',
            'channel_path' => 'customer.mobile',
            'token_resend_form' => 'dos_resend_confirmation_otp',
            'token_verify_form' => 'dos_verification_otp',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getResendModel()
    {
        return new OtpResend();
    }

    /**
     * {@inheritdoc}
     */
    public function getVerifyModel()
    {
        return new OtpVerification();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'otp';
    }
}
