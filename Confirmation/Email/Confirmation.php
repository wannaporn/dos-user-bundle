<?php

namespace DoS\UserBundle\Confirmation\Email;

use DoS\UserBundle\Confirmation\ConfirmationAbstract;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use DoS\UserBundle\Confirmation\Exception\NotFoundChannelException;
use DoS\UserBundle\Confirmation\Model\EmailResend;
use DoS\UserBundle\Confirmation\Model\EmailVerification;
use DoS\UserBundle\Confirmation\Model\VerificationInterface;
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
        $email = $subject->getConfirmationChannel($this->options['channel_path']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new NotFoundChannelException();
        }

        $this->sender->send(
            $this->options['token_send_template'],
            array($email),
            array('subject' => $subject, 'token' => $token)
        );
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
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            return $form;
        }

        if (!$this->validateTimeAware($subject = $data->getSubject())) {
            $form->addError(new FormError('ui.trans.user.confirmation.verify.invalid_time'));
        }

        if (!$form->getErrors(true)->count()) {
            $this->successVerify($subject);
        }

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function createVerifyForm()
    {
        return $this->formFactory->create(
            $this->options['token_verify_form'],
            $this->getVerifyModel(),
            array('csrf_protection' => false)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getResendModel()
    {
        return new EmailResend();
    }

    /**
     * {@inheritdoc}
     */
    public function getVerifyModel()
    {
        return new EmailVerification();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'token_resend_form' => 'dos_resend_confirmation_email',
            'token_verify_form' => 'dos_verification',
        ));
    }
}
