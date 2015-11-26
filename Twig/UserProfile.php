<?php

namespace DoS\UserBundle\Twig;

use DoS\UserBundle\Model\UserInterface;
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
    public function getUserAvartar(UserInterface $user = null, $filter = '70x70', array $runtimeConfig = array())
    {
        if (!$user) {
            return;
        }

        if ($avatar = $user->getProfilePicture()) {
            if (preg_match('/\/\//', $avatar)) {
                return $avatar;
            }

            if (empty($runtimeConfig)) {
                $runtimeConfig = array(
                    'thumbnail' => array(
                        "size" => explode('x', $filter),
                        "mode" => 'inset',
                    )
                );
            }

            /**
             * We must to define `sizing` filter first!
             * eg.
             *
             *   liip_imagine:
             *       filter_sets:
             *           sizing:
             *               data_loader: cmf_media_doctrine_phpcr
             *           filters:
             *               thumbnail: { size: [200, 200], mode: inset }
             */
            return $this->cacheManager->getBrowserPath($avatar, 'sizing', $runtimeConfig);
        }

        return;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'ui_user_profile';
    }
}
