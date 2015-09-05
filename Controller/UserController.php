<?php

namespace DoS\UserBundle\Controller;

use DoS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends ConfirmationController
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
     * @return Response
     */
    public function searchAction(Request $request)
    {
        return $this->indexAction($request);
    }
}
