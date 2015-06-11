<?php

namespace DoS\UserBundle\Confirmation\Email;

use DoS\UserBundle\Confirmation\ConfirmationAbstract;
use DoS\UserBundle\Confirmation\ConfirmationSubjectInterface;
use DoS\UserBundle\Confirmation\Exception\NotFoundChannelException;
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
    protected function verifyToken(ConfirmationSubjectInterface $subject, array $options = array())
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'email';
    }
}
