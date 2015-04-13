<?php

namespace DoS\UserBundle\Model;

interface UuidAwareInterface
{
    public function getId();

    public function setId($id);
}
