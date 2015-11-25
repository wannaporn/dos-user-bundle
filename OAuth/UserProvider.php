<?php

namespace DoS\UserBundle\OAuth;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use DoS\UserBundle\Model\UserOAuthInterface;
use DoS\UserBundle\Model\UserInterface as DoSUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sylius\Bundle\UserBundle\OAuth\UserProvider as SyliusUserProvider;
use Sylius\Component\User\Model\CustomerInterface;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 */
class UserProvider extends SyliusUserProvider
{
    /**
     * @param UserResponseInterface|ResourceResponse $response
     *
     * @return UserInterface
     */
    protected function createUserByOAuthUserResponse(UserResponseInterface $response)
    {
        /** @var DoSUserInterface $user */
        $user = $this->userFactory->createNew();
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();

        // set default values taken from OAuth sign-in provider account
        // todo: check security configuration provide by `fos....username_email`
        if (null === $response->getEmail()) {
            throw new AccountNoEmailException();
        }

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $customer->setEmail($email);
        }

        if (!$user->getUsername()) {
            $user->setUsername($response->getEmail() ?: $response->getNickname());
        }

        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setDisplayName($response->getNickname());
        $user->setLocale($response->getLocale());
        $user->setCustomer($customer);
        $user->confirmed();

        $customer->setFirstName($response->getFirstName());
        $customer->setLastName($response->getLastName());
        $customer->setGender($response->getGender() ?: CustomerInterface::UNKNOWN_GENDER);
        $customer->setBirthday($response->getBirthday());

        return $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * {@inheritdoc}
     */
    protected function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response)
    {
        /** @var UserOAuthInterface $oauth */
        $oauth = $this->oauthFactory->createNew();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setProfilePicture($response->getProfilePicture());

        /* @var DoSUserInterface $user */
        $user->addOAuthAccount($oauth);

        $this->userManager->persist($user);
        $this->userManager->flush();

        return $user;
    }
}
