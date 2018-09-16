<?php

namespace Erfans\AssetBundle\Config;

class AssetConfig {

    /** @var array $config */
    private $config;

    public function __construct(array $config = []) {

        $this->config = $config;
    }

    private function getConfig($key) {
        return key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->getConfig("id");
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->getConfig("version");
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->getConfig("alias");
    }

    /**
     * @return string
     */
    public function getBundle() {
        return $this->getConfig("bundle");
    }

    /**
     * @return string
     */
    public function getInstaller() {
        return $this->getConfig("installer");
    }

    /**
     * @return string
     */
    public function getOutputDirectory() {
        return $this->getConfig("output_directory");
    }
}
