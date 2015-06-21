<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
use DoS\UserBundle\Confirmation\Exception\ConfirmationException;
use DoS\UserBundle\Model\UserInterface;
use Sylius\Bundle\UserBundle\Controller\UserController as BaseUserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends BaseUserController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function changeStateAction(Request $request)
    {
        /** @var UserInterface $resource */
        $resource = $this->findOr404($request);
        $resource->setEnabled((bool) $request->query->get('state'));
        $this->domainManager->update($resource);

        return $this->redirectHandler->redirectToReferer();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetPasswordAction(Request $request)
    {
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function searchAction(Request $request)
    {
        return parent::indexAction($request);
    }

    protected function trans($key, $parameters = array(), $domain = null)
    {
        return $this->get('translator')->trans($key, $parameters, $domain);
    }

    public function resendAction(Request $request)
    {
        $confirmation = $this->getConfirmationService();
        $token = $request->get('token') ?: $confirmation->getStoredToken();
        $subject = $confirmation->findSubject($token);

        // TODO: may find subject by `email`, `mobile` ??

        try {
            if ($confirmation->canResend($subject)) {
                $confirmation->send($subject);
            }
        } catch (\Exception $e) {
        }

        return $this->redirectToRoute($confirmation->getConfirmRoute());

        //return $this->confirmationAction($request);
    }

    public function confirmationAction(Request $request)
    {
        $confirmation = $this->getConfirmationService();
        $token = $confirmation->getStoredToken(/*TODO: true*/);
        $view = $this->view(null, 200)->setTemplate($confirmation->getTokenConfirmTemplate());

        $data = array(
            'error' => false,
            'token' => $token,
            'type' => $confirmation->getType(),
        );

        if (!$token || !$subject = $confirmation->findSubject($token)) {
            $data['error'] = $this->trans('ui.trans.user.confirmation.invalid_token');
            $view->setStatusCode(400);
        } else {
            $data['subject'] = $subject;
            $data['time_aware'] = $confirmation->getTokenTimeAware($subject);
        }

        return $this->handleView($view->setData($data));
    }

    public function verificationAction(Request $request, $token)
    {
        $confirmation = $this->getConfirmationService();
        $options = $request->request->all();
        $view = $this->view()->setTemplate($confirmation->getTokenVerifyTemplate());

        $data = array(
            'error' => false,
            'token' => $token,
            'type' => $confirmation->getType(),
        );

        try {
            $data['subject'] = $confirmation->verify($token, $options);
        } catch (ConfirmationException $e) {
            $data['error'] = $this->trans($e->getMessage());
            $view->setStatusCode(400);
        }

        return $this->handleView($view->setData($data));
    }

    /**
     * @return ConfirmationInterface|null
     *
     * @throws \Exception
     */
    protected function getConfirmationService()
    {
        return $this->get('dos.user.confirmation.factory')->createActivedConfirmation(true);
    }
}
