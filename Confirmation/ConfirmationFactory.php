<?php

namespace DoS\UserBundle\Confirmation;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;

class ConfirmationFactory
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var null|string
     */
    protected $activedService = null;

    /**
     * @var SettingsManagerInterface
     */
    protected $settingsManager;

    public function __construct(SettingsManagerInterface $settingsManager)
    {
        $this->settingsManager = $settingsManager;
        // TODO: apply options from settings
    }

    /**
     * Load settings parameter for given namespace and name.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function getSettingsParameter($name)
    {
        if (false === strpos($name, '.')) {
            throw new \InvalidArgumentException(
                sprintf('Parameter must be in format "namespace.name", "%s" given.', $name)
            );
        }

        list($namespace, $name) = explode('.', $name);

        $settings = $this->settingsManager->loadSettings($namespace);

        return $settings->get($name);
    }

    /**
     * @param $name
     *
     * @throws \Exception
     */
    public function setActivedService($name)
    {
        if (!$this->has($name)) {
            throw new \Exception(sprintf('Cannot set unregistered confirmation type "%s".', $name));
        }

        $this->activedService = $name;
    }

    /**
     * @param ConfirmationInterface $confirmation
     * @param array                 $options
     */
    public function add(ConfirmationInterface $confirmation, array $options = array())
    {
        $this->types[$confirmation->getType()] = $confirmation;

        if (!empty($options)) {
            $this->options[$confirmation->getType()] = $options;
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->types);
    }

    /**
     * @param $name
     *
     * @return ConfirmationInterface
     * @throws \Exception
     */
    public function create($name)
    {
        if (!$this->has($name)) {
            throw new \Exception(sprintf('Not found confirmation type "%s".', $name));
        }

        /** @var ConfirmationInterface $instance */
        $instance = $this->types[$name];

        if (array_key_exists($name, $this->options)) {
            $instance->resetOptions($this->options[$name]);
        }

        return $instance;
    }
}
