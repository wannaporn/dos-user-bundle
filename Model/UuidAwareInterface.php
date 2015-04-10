<?php

namespace Dos\UserBundle\Model;

interface UuidAwareInterface
{
    public function getId();

    public function setId($id);
}
