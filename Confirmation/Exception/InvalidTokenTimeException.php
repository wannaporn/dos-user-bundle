<?php

namespace DoS\UserBundle\Confirmation\Exception;

class InvalidTokenTimeException extends ConfirmationException
{
    protected $message = 'Invalid token time.';
}
