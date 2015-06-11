<?php

namespace DoS\UserBundle\Controller;

use DoS\ResourceBundle\Controller\ResourceController;
use DoS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends ResourceController
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
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $this->findOr404($request);

        $form = $formFactory->createForm();
        $form->setData($user);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH'))
            && $form->submit($request, !$request->isMethod('PATCH'))->isValid()
        ) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch('dos.user.resetting.reset.success', $event);

            $userManager->updateUser($user);
            $response = $this->redirectHandler->redirectTo($user);

            $dispatcher->dispatch('dos.user.resetting.reset.completed',
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('resetPassword.html'))
            ->setData(array(
                $this->config->getResourceName() => $user,
                'form' => $form->createView(),
            ));

        return $this->handleView($view);
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
}
