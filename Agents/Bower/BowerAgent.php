<?php

namespace Erfans\AssetBundle\Agents\Bower;

use Bowerphp\Bowerphp;
use Bowerphp\Installer\Installer;
use Bowerphp\Output\BowerphpConsoleOutput;
use Bowerphp\Package\Package;
use Bowerphp\Repository\GithubRepository;
use Bowerphp\Util\ZipArchive;
use Erfans\AssetBundle\Agents\BaseAgent;
use Erfans\AssetBundle\Agents\InstallerInterface;
use Github\Client;
use Bowerphp\Config\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;
use Bowerphp\Util\Filesystem as BowerPhpFileSystem;

class BowerAgent extends BaseAgent implements InstallerInterface
{

    /** @var array $configKeysMap keys map to convert Yaml keys to bowers */
    protected $configKeysMap = [
        "bower_config" => [
            "shorthand_resolver" => "shorthand-resolver",
            "https_proxy" => "https-proxy",
            "user_agent" => "user-agent",
            "strict_ssl" => "strict-ssl",
            "shallow_clone_hosts" => "shallowCloneHosts",
            "ignored_dependencies" => "ignoredDependencies",
        ],
        "package_config" => [
            "module_type" => "moduleType",
            "dev_dependencies" => "devDependencies",
        ],
    ];

    /** @var array $environmentConfig */
    private $environmentConfig;

    /** @var array $config */
    private $config;

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var string $cachePath */
    private $cachePath;

    /** @var string $downloadPath */
    private $downloadPath;

    /** @var  string $githubToken */
    private $githubToken;

    /**
     * Agent constructor.
     *
     * @param String $rootDirectory
     * @param array $config
     */
    public function __construct($rootDirectory, array $config)
    {
        $config = $this->normalizeConfig($config, $this->configKeysMap);

        $this->config = $config["package"];
        $this->environmentConfig = $config["bower"];
        $this->cachePath = $config["cache_path"];
        $this->downloadPath = $config["directory"];
        $this->githubToken = key_exists("github_token", $config) ? $config["github_token"] : null;

        $this->rootDirectory = $rootDirectory;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     *
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     * @throws \Exception
     */
    public function install(array $assetConfigs)
    {
        // base folder will generate based on current environment
        $this->mkdir($this->cachePath);

        // create bower.json file
        // add dependencies of bundles to copy of main config
        $config = $this->config;
        foreach ($assetConfigs as $assetConfig) {
            $config["dependencies"][$assetConfig->getId()] = $assetConfig->getVersion();
        }

        $this->dumpFile($this->cachePath."/bower.json", $this->convertArrayToJsonObject($config));

        // create .bowerrc file
        $envConfig = $this->environmentConfig;
        // fix relative path to web directory
        $fileSystem = new Filesystem();
        $endPath = $this->rootDirectory."/../".$this->downloadPath;
        $envConfig["directory"] = $fileSystem->makePathRelative($endPath, $this->cachePath);
        $this->dumpFile($this->cachePath."/.bowerrc", $this->convertArrayToJsonObject($envConfig));

        // Bowerphp install command
        $oldWorkingDir = getcwd();
        chdir($this->cachePath);

        $bowerFileSystem = new BowerPhpFileSystem();
        $bowerConfig = new Config($bowerFileSystem);
        $githubClient = new Client();

        if (!empty($this->githubToken)) {
            $githubClient->authenticate($this->githubToken, null, Client::AUTH_HTTP_TOKEN);
        }

        $installer = new Installer($bowerFileSystem, new ZipArchive(), $bowerConfig);
        $bowerOutput = new BowerphpConsoleOutput($this->getOutput());

        $bowerPhp = new Bowerphp($bowerConfig, $bowerFileSystem, $githubClient, new GithubRepository(), $bowerOutput);

        $bowerPhp->installDependencies($installer);

        foreach ($assetConfigs as $assetConfig) {
            $package = new Package($assetConfig->getId());

            $directory = $bowerConfig->getInstallDir().'/'.$package->getName();
            $assetConfig->setInstalledDirectory($directory);

            try {
                $bowerInfo = $bowerConfig->getPackageBowerFileContent($package);
            } catch (\RuntimeException $e) {
                $this->getOutput()->writeln($e->getMessage());
                continue;
            }

            if (array_key_exists("version", $bowerInfo)) {
                $assetConfig->setInstalledVersion($bowerInfo["version"]);
            }

            if ($assetConfig->getMainFiles() == null && array_key_exists("main", $bowerInfo)) {
                $assetConfig->setMainFiles(
                    is_array($bowerInfo["main"]) ?
                        $bowerInfo["main"] :
                        [$bowerInfo["main"]]
                );
            }
        }

        chdir($oldWorkingDir);

        return $assetConfigs;
    }
}
