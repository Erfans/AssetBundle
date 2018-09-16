<?php

namespace Erfans\AssetBundle\Agents\File;

use App\Erfans\AssetBundle\Asset\ConfigurableInterface;
use Erfans\AssetBundle\Agents\BaseAgent;
use Erfans\AssetBundle\Agents\InstallerInterface;
use Erfans\AssetBundle\Config\AssetConfig;
use Erfans\AssetBundle\Util\FileSystem;

class FileAgent extends BaseAgent implements InstallerInterface, ConfigurableInterface {

    /** @var array $configKeysMap keys map to convert Yaml keys to bowers */
    private $configKeysMap = [];

    /** @var array $config */
    private $config;

    /** @var \Erfans\AssetBundle\Util\FileSystem $fileSystem */
    private $fileSystem;

    /**
     * Agent constructor.
     */
    public function __construct(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Set a related configuration to the agent from erfans_asset config
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config) {
        $config = $this->normalizeConfig($config, $this->configKeysMap);

        $this->config = $config;
    }

    /**
     * @param AssetConfig[] $assetConfigs
     *
     * @return AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs) {

        /** @var AssetConfig $assetConfig */
        foreach ($assetConfigs as $assetConfig) {

            $outputDirectory = $assetConfig->getOutputDirectory();
            $this->fileSystem->mkdir($outputDirectory);

            if (array_key_exists("create_directory", $this->config) && $this->config["create_directory"]) {
                $outputDirectory .= "/".$assetConfig->getAlias();
                $this->fileSystem->mkdir($outputDirectory);
            }

            $fileName = basename($assetConfig->getId());
            $filePath = $outputDirectory."/".$fileName;

            if ($this->fileSystem->exists($filePath)) {
                $this->logger->info("File '$fileName' is already installed.");
            } else {
                $this->fileSystem->copy($assetConfig->getId(), $filePath);
            }
        }

        return $assetConfigs;
    }
}
