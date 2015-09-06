<?php

namespace DoS\UserBundle\Confirmation;

interface ConfirmationSubjectFinderInterface
{
    public function findConfirmationSubject($propertyPath, $value);
}
