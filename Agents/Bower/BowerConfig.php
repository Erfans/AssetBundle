<?php

namespace App\Erfans\AssetBundle\Agents\Bower;

use Bowerphp\Config\ConfigInterface;
use Bowerphp\Package\PackageInterface;
use Erfans\AssetBundle\Util\FileSystem;
use RuntimeException;

class BowerConfig implements ConfigInterface {

    private $cacheDir;

    private $installDir;

    private $filesystem;

    private $basePackagesUrl = 'http://registry.bower.io/packages/';

    private $saveToBowerJsonFile = false;

    private $bowerFileConfig;

    public function __construct($cacheDir, $installDir, $bowerFileConfig, FileSystem $fileSystem) {
        $this->cacheDir = $cacheDir;
        $this->installDir = $installDir;
        $this->filesystem = $fileSystem;
        $this->bowerFileConfig = $bowerFileConfig;
    }

    /**
     * @return string
     */
    public function getBasePackagesUrl() {
        return $this->basePackagesUrl;
    }

    /**
     * @return string
     */
    public function getCacheDir() {
        return $this->cacheDir;
    }

    /**
     * @return string
     */
    public function getInstallDir() {
        return $this->installDir;
    }

    /**
     * @return bool
     */
    public function isSaveToBowerJsonFile() {
        return $this->saveToBowerJsonFile;
    }

    /**
     * Set true|false for decide if add package reference on bower.json file during install procedure
     *
     * @param bool $flag default true
     */
    public function setSaveToBowerJsonFile($flag = true) {
        $this->saveToBowerJsonFile = $flag;
    }

    /**
     * Init project's bower.json file
     *
     * @param array $params
     *
     * @return int
     */
    public function initBowerJsonFile(array $params) {
        // not required
        return 0;
    }

    /**
     * Update project's bower.json with a new added package
     *
     * @param PackageInterface $package
     *
     * @return int
     */
    public function updateBowerJsonFile(PackageInterface $package) {
        // not required
        return 0;
    }

    /**
     * Update project's bower.json from a previous existing one
     *
     * @param array $old values of previous bower.json
     * @param array $new new values
     *
     * @return int
     */
    public function updateBowerJsonFile2(array $old, array $new) {
        // not required
        return 0;
    }

    /**
     * Get content from project's bower.json file
     *
     * @return array
     * @throws \Exception if bower.json does not exist
     */
    public function getBowerFileContent() {
        return $this->bowerFileConfig;
    }

    /**
     * Retrieve the array of overrides optionally defined in the bower.json file.
     * Each element's key is a package name, and contains an array of other package names
     * and versions that should replace the dependencies found in that package's canonical bower.json
     *
     * @return array The overrides section from the bower.json file, or an empty array if no overrides section is
     *               defined
     */
    public function getOverridesSection() {
        return [];
    }

    /**
     * Get the array of overrides defined for the specified package
     *
     * @param string $packageName The name of the package for which dependencies are being overridden
     *
     * @return array A list of dependency name => override versions to be used instead of the target package's normal
     *               dependencies.  An empty array if none are defined
     */
    public function getOverrideFor($packageName) {
        return [];
    }

    /**
     * Get content from a packages' bower.json file
     *
     * @param PackageInterface $package
     *
     * @return array
     * @throws \Exception if bower.json or package.json does not exist in a dir of installed package
     */
    public function getPackageBowerFileContent(PackageInterface $package) {
        $file = $this->getInstallDir().'/'.$package->getName().'/.bower.json';
        if (!$this->filesystem->exists($file)) {
            throw new RuntimeException(sprintf('Could not find .bower.json file for package %s.', $package->getName()));
        }
        $bowerJson = $this->filesystem->getContent($file);
        $bower = json_decode($bowerJson, true);
        if (is_null($bower)) {
            throw new RuntimeException(sprintf('Invalid content in .bower.json for package %s.', $package->getName()));
        }

        return $bower;
    }

    /**
     * Check if project's bower.json file exists
     *
     * @return bool
     */
    public function bowerFileExists() {
        // manipulate the system
        return true;
    }
}
