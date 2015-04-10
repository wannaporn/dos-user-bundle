<?php

namespace Dos\UserBundle\Model;

interface UserUpdaterAwareInterface
{
    public function setUpdater($updater);

    public function getUpdater();
}
