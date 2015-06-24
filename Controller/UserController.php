<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
use DoS\UserBundle\Confirmation\Exception\ConfirmationException;
use DoS\UserBundle\Model\CustomerInterface;
use DoS\UserBundle\Model\UserInterface;
use libphonenumber\PhoneNumberUtil;
use Sylius\Bundle\UserBundle\Controller\UserController as BaseUserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends BaseUserController
{
    /**
     * @param $key
     * @param array $parameters
     * @param null  $domain
     *
     * @return string
     */
    private function trans($key, $parameters = array(), $domain = null)
    {
        return $this->get('translator')->trans($key, $parameters, $domain);
    }

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
     * @return Response
     */
    public function searchAction(Request $request)
    {
        return $this->indexAction($request);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resendAction(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');
        $mobile = $request->get('mobile');
        $username = $request->get('username');

        $confirmation = $this->getConfirmationService();
        $customerEr = $this->get('dos.repository.customer');
        $userEr = $this->get('dos.repository.user');

        switch (true) {
            case !empty($email):
                $subject = $customerEr->findOneBy(array('email' => $email));
                break;

            case !empty($mobile):
                $number = PhoneNumberUtil::getInstance()->parse($mobile, 'TH');
                $subject = $customerEr->findOneBy(array('mobile' => $number));
                break;

            case !empty($username):
                $subject = $userEr->findOneBy(array('username' => $username));
                break;

            default:
                $token = $token ?: $confirmation->getStoredToken();
                $subject = $confirmation->findSubject($token);
                break;
        }

        $error = array();

        try {
            if (empty($subject)) {
                throw new NotFoundHttpException('Not found subject for confirmation.');
            }

            if ($subject instanceof CustomerInterface) {
                $subject = $subject->getUser();
            }

            $confirmation->canResend($subject, true);
            $confirmation->send($subject);
        } catch (\Exception $e) {
            // Nothing to do.
            $error['message'] = $e->getMessage();
        }

        return $this->redirectToRoute($confirmation->getConfirmRoute(), $error);
    }

    /**
     * @return Response
     */
    public function confirmationAction()
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

    /**
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
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
