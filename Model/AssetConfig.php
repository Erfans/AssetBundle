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
    private $mainFiles;

    /** @var  string $installedVersion */
    private $installedVersion;

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


}