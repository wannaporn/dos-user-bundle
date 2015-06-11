<?php

namespace DoS\UserBundle\Confirmation\Email;

use DoS\UserBundle\Confirmation\SenderInterface;
use Sylius\Component\Mailer\Sender\Sender as MailSender;

class Sender extends MailSender implements SenderInterface
{

}
