<?php

namespace DoS\UserBundle\Confirmation\OTP;

use DoS\UserBundle\Confirmation\SenderInterface;
use SmsSender\SmsSenderInterface;

class Sender implements SenderInterface
{
    /**
     * @var SmsSenderInterface
     */
    protected $sender;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(SmsSenderInterface $sender, \Twig_Environment $twig)
    {
        $this->sender = $sender;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    public function send($template, array $recipients, array $data = array())
    {
        $data = $this->twig->mergeGlobals($data);
        $template = $this->twig->loadTemplate($template);
        $content = $template->renderBlock('content', $data);
        $originator = $template->renderBlock('originator', $data);

        $this->sender->send($recipients[0], $content, $originator);
    }
}
