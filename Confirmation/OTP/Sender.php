<?php

namespace DoS\UserBundle\Confirmation\OTP;

use DoS\UserBundle\Confirmation\SenderInterface;

class Sender implements SenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function send($code, array $recipients, array $data = array())
    {
        // TODO: Implement send() method.
    }
}
