<?php

namespace DoS\UserBundle\Authorization;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\SettingsInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface;

/**
 * Test (toggleable) authorization checker.
 */
class ToggleableAuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var SettingsManagerInterface
     */
    private $settingsManager;

    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param SettingsManagerInterface $settingsManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, SettingsManagerInterface $settingsManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->settingsManager = $settingsManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode)
    {
        if (null === $this->settings) {
            $this->settings = $this->settingsManager->load('sylius_security');
        }

        if (false === $this->settings->get('enabled')) {
            return true;
        }

        return $this->authorizationChecker->isGranted($permissionCode);
    }
}
