<?php

namespace DoS\UserBundle\Controller;

use Sylius\Bundle\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends BaseSecurityController
{
    /**
     * @var string
     */
    protected $alreadyLoginRoute;

    /**
     * @var string
     */
    protected $alreadyLoginUrl;

    /**
     * @var string
     */
    protected $firewallName = 'main';

    /**
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    protected function checkAlreadyLogin(Request $request)
    {
        if ($this->get('dos.user.security')->isLoggedIn()) {
            if ($this->alreadyLoginUrl) {
                return $this->redirect($this->alreadyLoginUrl);
            }

            if ($this->alreadyLoginRoute) {
                return $this->redirectToRoute($this->alreadyLoginRoute);
            }

            return $this->redirect($request->headers->get('referer', $request->getUriForPath('/')));
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function loginAction(Request $request)
    {
        if ($response = $this->checkAlreadyLogin($request)) {
            return $response;
        }

        $key = sprintf('_security.%s.target_path', $this->firewallName);
        if (!$request->getSession()->has($key)) {
            $request->getSession()->set($key, $request->headers->get('referer'));
        }

        return parent::loginAction($request);
    }
}
