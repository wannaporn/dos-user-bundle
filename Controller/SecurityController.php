<?php

namespace DoS\UserBundle\Controller;

use Sylius\Bundle\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseSecurityController
{
    /**
     * @var string
     */
    protected $alreadyLoginRoute;

    /**
     * @param Request $request
     *
     * @return RedirectResponse|void
     */
    protected function checkAlreadyLogin(Request $request)
    {
        if ($this->get('dos.user.security')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            if ($this->alreadyLoginRoute) {
                return $this->redirectToRoute($this->alreadyLoginRoute);
            }

            return $this->redirect($request->getUriForPath('/'));
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

        return parent::loginAction($request);
    }
}
