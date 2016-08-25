<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 8/25/2016
 * Time: 16:08
 */

namespace Erfans\AssetBundle\Agents\File;


use Erfans\AssetBundle\Agents\BaseAgent;
use Erfans\AssetBundle\Agents\InstallerInterface;

class FileAgent extends BaseAgent implements InstallerInterface
{
    /** @var array $configKeysMap keys map to convert Yaml keys to bowers */
    protected $configKeysMap = [];

    /** @var array $config */
    private $config;

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var string $downloadPath */
    private $downloadPath;

    /**
     * Agent constructor.
     *
     * @param String $rootDirectory
     * @param array $config
     */
    public function __construct($rootDirectory, array $config)
    {
        $config = $this->normalizeConfig($config, $this->configKeysMap);

        $this->config = $config;
        $this->downloadPath = $config["directory"];

        $this->rootDirectory = $rootDirectory;
    }


    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs)
    {
        $endPath = $this->rootDirectory."/../".$this->downloadPath;

        $this->mkdir($endPath);

        foreach ($assetConfigs as $assetConfig) {

            $folderPath = $endPath;

            if (array_key_exists("create_directory", $this->config) && $this->config["create_directory"]) {
                $folderPath .= "/".$assetConfig->getAlias();
                $this->mkdir($folderPath);
            }

            $fileName = basename($assetConfig->getId());

            $filePath = $folderPath."/".$fileName;

            if ($this->exists($filePath)) {
                $this->logInfo("File '$fileName' is already installed.");
            } else {
                $this->streamDownload($assetConfig->getId(), $filePath);
            }

            $assetConfig->setInstalledDirectory($folderPath);
            $assetConfig->setMainFiles([$fileName]);
        }

        return $assetConfigs;
    }
}