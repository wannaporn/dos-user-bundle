<?php

namespace DoS\UserBundle\Confirmation\Email;

use DoS\UserBundle\Confirmation\SenderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface as MailSenderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Sender extends ContainerAware implements SenderInterface
{
    /**
     * @var MailSenderInterface
     */
    protected $sender;

    public function __construct(MailSenderInterface $sender)
    {
        $this->sender = $sender;
    }

    /**
     * {@inheritdoc}
     */
    public function send($code, array $recipients, array $data = array())
    {
        return $this->sender->send($code, $recipients, $data);
    }
}
