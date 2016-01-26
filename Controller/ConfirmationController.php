<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Confirmation\ConfirmationInterface;
use DoS\UserBundle\Model\UserInterface;
use FOS\RestBundle\View\View;
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
        $config = $this->requestConfigurationFactory->create($this->metadata, $request);

        $view = View::create(array(
            'type' => $confirmation->getType(),
            'form' => $form->createView()
        ))->setTemplate($config->getTemplate('resend'));

        return $this->viewHandler->handle($config, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function confirmationAction(Request $request)
    {
        $confirmation = $this->getConfirmationService();
        $token = $confirmation->getStoredToken(true);
        $subject = $confirmation->findSubjectWithToken($token);
        $config = $this->requestConfigurationFactory->create($this->metadata, $request);

        $view = View::create(array(
            'type' => $confirmation->getType(),
            'subject' => $subject,
            'subjectValue' => $confirmation->getSubjectValue($subject),
            'time_aware' => $confirmation->getTokenTimeAware($subject),
            'resendForm' => $confirmation->createResendForm()->createView(),
            'verifyForm' => $confirmation->createVerifyForm()->createView(),
        ))->setTemplate($config->getTemplate('confirmation'));

        return $this->viewHandler->handle($config, $view);
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
        $config = $this->requestConfigurationFactory->create($this->metadata, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $user */
            $user = $form->getData()->getSubject();
            $this->get('sylius.security.user_login')->login($user);
        }

        $view = View::create(array(
            'type' => $confirmation->getType(),
            'form' => $form->createView()
        ))->setTemplate($config->getTemplate('verification'));

        return $this->viewHandler->handle($config, $view);
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
