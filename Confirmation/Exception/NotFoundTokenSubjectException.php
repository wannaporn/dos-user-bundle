<?php

namespace DoS\UserBundle\Confirmation\Exception;

class NotFoundTokenSubjectException extends ConfirmationException
{
    protected $message = 'Not found token subject.';
}
