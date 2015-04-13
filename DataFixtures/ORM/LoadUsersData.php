<?php

namespace DoS\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use DoS\UserBundle\Doctrine\ORM\UserRepository;
use DoS\FixturesBundle\DataFixtures\DataFixture;
use DoS\UserBundle\Model\UserInterface;

/**
 * User fixtures.
 */
class LoadUsersData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->createUser(
            'nukboon@gmail.com',
            'mflv[0hk',
            true,
            array('ROLE_ADMINISTRATION_ACCESS')
        );

        $manager->persist($user);

        $this->setReference('DoS.Administrator', $user);

        $user = $this->createUser(
            'user@gmail.com',
            'mflv[',
            true
        );

        $manager->persist($user);

        $this->setReference('DoS.User', $user);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool   $enabled
     * @param array  $roles
     * @param string $currency
     *
     * @return UserInterface
     */
    protected function createUser($email, $password, $enabled = true, array $roles = array('ROLE_USER'), $currency = 'THB')
    {
        /* @var $user UserInterface */
        $user = $this->getUserRepository()->createNew();
        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setCurrency($currency);
        $user->setEnabled($enabled);

        return $user;
    }

    /**
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->get('dos.repository.user');
    }
}
