<?php

namespace DoS\UserBundle\Model;

use Sylius\Component\User\Model\GroupInterface as BaseGroupInterface;

interface GroupInterface extends BaseGroupInterface
{
    /**
     * @param mixed $color
     */
    public function setColor($color);

    /**
     * @return mixed
     */
    public function getColor();
}
