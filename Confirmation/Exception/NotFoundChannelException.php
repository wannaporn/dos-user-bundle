<?php

namespace DoS\UserBundle\Confirmation\Exception;

class NotFoundChannelException extends ConfirmationException
{
    protected $message = 'Not found confirmation channel.';
}
