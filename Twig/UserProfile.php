<?php

namespace Dos\UserBundle\Twig;

use Dos\UserBundle\Model\UserInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class UserProfile extends \Twig_Extension
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ui_user_avatar', array($this, 'getUserAvartar')),
        );
    }

    /**
     * @param UserInterface $user
     * @param string        $filter
     * @param array         $runtimeConfig
     *
     * @return null|string
     */
    public function getUserAvartar(UserInterface $user = null, $filter = 'avatar', array $runtimeConfig = array())
    {
        if (!$user) {
            return null;
        }

        if ($avatar = $user->getProfilePicture()) {
            if (preg_match('/\/\//', $avatar)) {
                return $avatar;
            }

            return $this->cacheManager->getBrowserPath($avatar, $filter, $runtimeConfig);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ui_user_profile';
    }
}
