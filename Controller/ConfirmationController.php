<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
use DoS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ConfirmationController extends SyliusUserController
{
    /**
     * @param $key
     * @param array $parameters
     * @param null  $domain
     *
     * @return string
     */
    protected function trans($key, $parameters = array(), $domain = null)
    {
        return $this->get('translator')->trans($key, $parameters, $domain);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function confirmationResendAction(Request $request)
    {
        $confirmation = $this->getConfirmationService();
        $form = $confirmation->resend($request);

        $view = $this->view(array(
            'type' => $confirmation->getType(),
            'form' => $form->createView()
        ))->setTemplate($this->config->getTemplate('resend'));

        return $this->handleView($view);
    }

    /**
     * @return Response
     */
    public function confirmationAction()
    {
        $confirmation = $this->getConfirmationService();
        $token = $confirmation->getStoredToken(true);
        $subject = $confirmation->findSubjectWithToken($token);

        $view = $this->view(array(
            'type' => $confirmation->getType(),
            'subject' => $subject,
            'subjectValue' => $confirmation->getSubjectValue($subject),
            'time_aware' => $confirmation->getTokenTimeAware($subject),
            'resendForm' => $confirmation->createResendForm()->createView(),
            'verifyForm' => $confirmation->createVerifyForm()->createView(),
        ))->setTemplate($this->config->getTemplate('confirmation'));

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function verificationAction(Request $request, $token)
    {
        $confirmation = $this->getConfirmationService();
        $form = $confirmation->verify($request, $token);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $user */
            $user = $form->getData()->getSubject();
            $this->get('sylius.security.user_login')->login($user);
        }

        $view = $this->view(array(
            'type' => $confirmation->getType(),
            'form' => $form->createView()
        ))->setTemplate($this->config->getTemplate('verification'));

        return $this->handleView($view);
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
