<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/7/2016
 * Time: 16:06
 */

namespace Erfans\AssetBundle\Model;


class AssetConfig
{
    /** @var  string $id */
    private $id;

    /** @var  string $version */
    private $version;

    /** @var  string $filePath */
    private $filePath;

    /** @var  string $alias */
    private $alias;

    /** @var string $bundle */
    private $bundle;

    /** @var  array $mainFiles */
    private $mainFiles = [];

    /** @var  string $installedVersion */
    private $installedVersion;

    /** @var  string $installer */
    private $installer;

    /** @var  string $installedDirectory */
    private $installedDirectory;

    /** @var  string $outputDirectory */
    private $outputDirectory;

    public function __construct(array $config = [])
    {
        if (key_exists("bundle", $config)) {
            $this->setBundle($config["bundle"]);
        }

        if (key_exists("id", $config)) {
            $this->setId($config["id"]);
        }

        if (key_exists("version", $config)) {
            $this->setVersion($config["version"]);
        }

        if (key_exists("alias", $config)) {
            $this->setAlias($config["alias"]);
        }

        if (key_exists("main_files", $config)) {
            $this->setMainFiles($config["main_files"]);
        }

        if (key_exists("installer", $config)) {
            $this->setInstaller($config["installer"]);
        }

        if (key_exists("installed_directory", $config)) {
            $this->setInstalledDirectory($config["installed_directory"]);
        }

        if (key_exists("output_directory", $config)) {
            $this->setOutputDirectory($config["output_directory"]);
        }
    }

    /**
     * @param string $id
     * @return AssetConfig
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param string $version
     * @return AssetConfig
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $filePath
     * @return AssetConfig
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $alias
     * @return AssetConfig
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $bundle
     * @return AssetConfig
     */
    public function setBundle($bundle)
    {
        $this->bundle = $bundle;

        return $this;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @param array $mainFiles
     * @return AssetConfig
     */
    public function setMainFiles(array $mainFiles)
    {
        $this->mainFiles = $mainFiles;

        return $this;
    }

    /**
     * @param string $mainFile
     * @return AssetConfig
     */
    public function addMainFiles($mainFile)
    {
        $this->mainFiles[] = $mainFile;

        return $this;
    }

    /**
     * @return array
     */
    public function getMainFiles()
    {
        return $this->mainFiles;
    }

    /**
     * @param string $installedVersion
     * @return AssetConfig
     */
    public function setInstalledVersion($installedVersion)
    {
        $this->installedVersion = $installedVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstalledVersion()
    {
        return $this->installedVersion;
    }

    /**
     * @param string $installer
     * @return AssetConfig
     */
    public function setInstaller($installer)
    {
        $this->installer = $installer;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstaller()
    {
        return $this->installer;
    }

    /**
     * @param string $installedDirectory
     * @return AssetConfig
     */
    public function setInstalledDirectory($installedDirectory)
    {
        $this->installedDirectory = $installedDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstalledDirectory()
    {
        return $this->installedDirectory;
    }

    /**
     * @param string $outputDirectory
     * @return AssetConfig
     */
    public function setOutputDirectory($outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }
}