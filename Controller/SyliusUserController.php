<?php

namespace DoS\UserBundle\Controller;

use Sylius\Bundle\UserBundle\Controller\UserController;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\TokenProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SyliusUserController extends UserController
{
    /**
     * @param $response
     *
     * @return RedirectResponse
     */
    private function checkRedirection($response)
    {
        if ($response instanceof RedirectResponse) {
            return $response->setTargetUrl($this->generateUrl($this->config->getRedirectRoute('config_me')));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResetPasswordRequestUrl($token)
    {
        if (is_numeric($token)) {
            return $this->generateUrl($this->config->getRedirectRoute('config_me'));
        }

        return $this->generateUrl($this->config->getRedirectRoute('config_me'));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleResetPasswordRequest(TokenProviderInterface $generator, UserInterface $user, $senderEvent)
    {
        // TODO: check time before re-submit request, should be time aware to send request
        return $this->checkRedirection(parent::handleResetPasswordRequest($generator, $user, $senderEvent));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleResetPassword(UserInterface $user, $newPassword)
    {
        return $this->checkRedirection(parent::handleResetPassword($user, $newPassword));
    }

    /**
     * {@inheritdoc}
     */
    protected function handleChangePassword(UserInterface $user, $newPassword)
    {
        return $this->checkRedirection(parent::handleChangePassword($user, $newPassword));
    }
}
