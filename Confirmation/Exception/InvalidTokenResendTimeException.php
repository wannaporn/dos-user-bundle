<?php

namespace DoS\UserBundle\Confirmation\Exception;

class InvalidTokenResendTimeException extends ConfirmationException
{
    protected $message = 'Invalid token resend time.';

    /**
     * @var \DateTime
     */
    protected $time;

    /**
     * @var string
     */
    protected $timeAware;

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getTimeAware()
    {
        return $this->timeAware;
    }

    /**
     * @param string $timeAware
     */
    public function setTimeAware($timeAware)
    {
        $this->timeAware = $timeAware;
    }
}
