<?php

namespace DoS\UserBundle\Model;

use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * Group model.
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @var string
     */
    protected $color;

    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * @param mixed $color
     *
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }
}
