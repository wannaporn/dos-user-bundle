<?php

namespace DoS\UserBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use DoS\UserBundle\Model\UserInterface;

class Security
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return TokenStorageInterface
     */
    private function getStorage()
    {
        return $this->container->get('security.token_storage');
    }

    /**
     * @return AuthorizationCheckerInterface
     */
    private function getAuthenChecker()
    {
        return $this->container->get('security.authorization_checker');
    }

    /**
     * @return null|TokenInterface
     */
    public function getToken()
    {
        return $this->getStorage()->getToken();
    }

    /**
     * @return string|void
     */
    public function getUsername()
    {
        $token = $this->getToken();

        if ($token instanceof TokenInterface) {
            return $token->getUsername();
        }

        return;
    }

    /**
     * @return UserInterface|void
     */
    public function getUser()
    {
        $token = $this->getToken();

        if ($token instanceof TokenInterface) {
            if ($token instanceof AnonymousToken || $token->getUser() === 'anon.') {
                return;
            }

            return $token->getUser();
        }

        return;
    }

    /**
     * @param      $attributes
     * @param null $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->getAuthenChecker()->isGranted($attributes, $object);
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }
}
