<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
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

    public function confirmationAction(Request $request)
    {
        $confirmation = $this->getConfirmationService();
        $token = $confirmation->getStoredToken(/*true*/);

        if (!$token || !$subject = $confirmation->findSubject($token)) {
            return $this->redirectToRoute($confirmation->getFailbackRoute());
        }

        return $this->render($confirmation->getTokenConfirmTemplate(), array(
            'subject' => $subject,
        ));
    }

    /**
     * @return ConfirmationInterface|null
     * @throws \Exception
     */
    protected function getConfirmationService()
    {
        return $this->get('dos.user.confirmation.factory')->createActivedConfirmation(true);
    }
}
