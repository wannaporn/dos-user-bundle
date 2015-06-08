<?php

namespace DoS\UserBundle\Confirmation\Exception;

class InvalidTokenVerifyException extends ConfirmationException
{
    protected $message = 'Invalid token verify.';
}
