<?php

namespace DoS\UserBundle\Model;

use Sylius\Component\User\Model\Group as BaseGroup;

/**
 * Group model.
 */
class Group extends BaseGroup implements GroupInterface
{
    /**
     * @var string
     */
    protected $color;

    /**
     * {@inheritdoc}
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * {@inheritdoc}
     */
    public function getColor()
    {
        return $this->color;
    }
}
