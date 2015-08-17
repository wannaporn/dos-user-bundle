<?php

namespace DoS\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RegisteredUser extends Constraint
{
    public $message;
    public $emailMessage;
    public $mobileMessage;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'dos_user_registration_validator';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
