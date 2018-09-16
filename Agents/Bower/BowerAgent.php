<?php

namespace Erfans\AssetBundle\Agents\Bower;

use App\Erfans\AssetBundle\Agents\Bower\BowerConfig;
use App\Erfans\AssetBundle\Asset\ConfigurableInterface;
use Bowerphp\Bowerphp;
use Bowerphp\Installer\Installer;
use Bowerphp\Output\BowerphpConsoleOutput;
use Bowerphp\Package\Package;
use Bowerphp\Repository\GithubRepository;
use Bowerphp\Util\ZipArchive;
use Erfans\AssetBundle\Agents\BaseAgent;
use Erfans\AssetBundle\Agents\InstallerInterface;
use Erfans\AssetBundle\DependencyInjection\Configuration;
use Erfans\AssetBundle\Util\FileSystem;
use Github\Client;
use Bowerphp\Util\Filesystem as BowerPhpFileSystem;

class BowerAgent extends BaseAgent implements InstallerInterface, ConfigurableInterface {

    /** @var array $configKeysMap keys map to convert Yaml keys to bowers */
    protected $configKeysMap = [
        "bower_config" => [
            "shorthand_resolver"   => "shorthand-resolver",
            "https_proxy"          => "https-proxy",
            "user_agent"           => "user-agent",
            "strict_ssl"           => "strict-ssl",
            "shallow_clone_hosts"  => "shallowCloneHosts",
            "ignored_dependencies" => "ignoredDependencies",
        ],
    ];

    /** @var array $environmentConfig */
    private $environmentConfig;

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var FileSystem $fileSystem */
    private $fileSystem;

    /** @var string $cachePath */
    private $cachePath;

    /** @var string $defaultInstallDirectory */
    private $defaultInstallDirectory;

    /** @var  string $githubToken */
    private $githubToken;

    /**
     * Agent constructor.
     *
     * @param String                              $rootDirectory
     * @param \Erfans\AssetBundle\Util\FileSystem $fileSystem
     */
    public function __construct($rootDirectory, FileSystem $fileSystem) {
        $this->rootDirectory = $rootDirectory;
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

        $this->cachePath = $config["cache_path"];
        $this->defaultInstallDirectory = $config["default_install_directory"];
        $this->githubToken = key_exists("github_token", $config) ? $config["github_token"] : null;

        $this->environmentConfig = $config["bower"];
    }

    /**
     * @param \Erfans\AssetBundle\Config\AssetConfig[] $assetConfigs
     *
     * @return \Erfans\AssetBundle\Config\AssetConfig[] assetConfigs
     * @throws \Exception
     */
    public function install(array $assetConfigs) {

        // base folder will generate based on current environment
        $this->fileSystem->mkdir($this->cachePath);

        foreach ($assetConfigs as $assetConfig) {

            $installDirectory = $assetConfig->getInstallDirectory();

            // log
            $this->logger->info("Start downloading ".$assetConfig->getAlias()." for bundle ".$assetConfig->getBundle());
            $this->logger->info("Install directory ".$installDirectory);

            // Bowerphp install command
            $bowerConfig = new BowerConfig($this->cachePath, $installDirectory, [], $this->fileSystem);
            $githubClient = new Client();

            if (!empty($this->githubToken)) {
                $githubClient->authenticate($this->githubToken, null, Client::AUTH_HTTP_TOKEN);
            }

            $installer = new Installer(new BowerPhpFileSystem(), new ZipArchive(), $bowerConfig);
            $bowerPhp = new Bowerphp(
                $bowerConfig,
                new BowerPhpFileSystem(),
                $githubClient,
                new GithubRepository(),
                new BowerphpConsoleOutput($this->consoleOutput)
            );

            $name = $assetConfig->getId();
            $requiredVersion = $assetConfig->getVersion();
            if (false !== strpos($requiredVersion, 'github')) {
                list($name, $requiredVersion) = explode('#', $assetConfig->getVersion());
            }
            $package = new Package($name, $requiredVersion);
            $bowerPhp->installPackage($package, $installer, true);
        }

        return $assetConfigs;
    }
}
