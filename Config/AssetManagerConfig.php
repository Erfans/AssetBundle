<?php

namespace Erfans\AssetBundle\Config;

class AssetManagerConfig {

    /** @var array $config */
    private $config;

    /**
     * AssetManagerConfig constructor.
     *
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
    }

    private function getConfig($key) {
        return key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * Is all bundles are added
     *
     * @return boolean
     */
    public function isAllBundles() {
        return $this->getConfig("all_bundles");
    }

    /**
     * get names of registered bundles
     *
     * @return array
     */
    public function getBundles() {
        return $this->getConfig("bundles");
    }

    /**
     * get the default output directory
     *
     * @return string
     */
    public function getDefaultInstallDirectory() {
        return $this->getConfig("default_install_directory");
    }

    /**
     * @param $agent
     *
     * @return string|null
     */
    public function getAgentDefaultInstallDirectory($agent) {
        $agentConfig = $this->getConfig($agent);
        return $agentConfig && isset($agentConfig["default_install_directory"]) ?
            $agentConfig["default_install_directory"] : null;
    }
}
