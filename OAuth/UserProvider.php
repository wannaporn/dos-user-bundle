<?php

namespace DoS\UserBundle\OAuth;

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use DoS\UserBundle\Model\UserOAuthInterface;
use DoS\UserBundle\Model\UserInterface as CoreUserInterface;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 */
class UserProvider extends FOSUBUserProvider
{
    /**
     * @var RepositoryInterface
     */
    protected $oauthRepository;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager     FOSUB user provider.
     * @param RepositoryInterface  $oauthRepository
     */
    public function __construct(
        UserManagerInterface $userManager,
        RepositoryInterface $oauthRepository
    ) {
        $this->userManager = $userManager;
        $this->oauthRepository = $oauthRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $oauth = $this->oauthRepository->findOneBy(array(
            'provider' => $response->getResourceOwner()->getName(),
            'identifier' => $response->getUsername(),
        ));

        if ($oauth instanceof UserOAuthInterface) {
            return $oauth->getUser();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userManager->findUserByEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }
        }

        return $this->createUserByOAuthUserResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        /* @var $user CoreUserInterface */
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Ad-hoc creation of user.
     *
     * @param UserResponseInterface|ResourceResponse $response
     *
     * @return CoreUserInterface
     */
    protected function createUserByOAuthUserResponse(UserResponseInterface $response)
    {
        /** @var CoreUserInterface $user */
        $user = $this->userManager->createUser();

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $user->setEmail($email);
        }

        if (!$user->getUsername()) {
            $user->setUsername($response->getEmail() ?: $response->getNickname());
        }

        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setEnabled(true);

        $user->setFirstName($response->getFirstName());
        $user->setLastName($response->getLastName());
        $user->setGender($response->getGender());
        $user->setFullName($response->getRealName());
        $user->setDisplayName($response->getNickname());
        $user->setLocale($response->getLocale());

        return $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Attach OAuth sign-in provider account to existing user.
     *
     * @param FOSUserInterface      $user
     * @param UserResponseInterface $response
     *
     * @return FOSUserInterface
     */
    protected function updateUserByOAuthUserResponse(
        FOSUserInterface $user,
        UserResponseInterface $response
    ) {
        /** @var UserOAuthInterface $oauth */
        $oauth = $this->oauthRepository->createNew();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setProfilePicture($response->getProfilePicture());

        /* @var $user CoreUserInterface */
        $user->addOAuthAccount($oauth);

        $this->userManager->updateUser($user);

        return $user;
    }
}
