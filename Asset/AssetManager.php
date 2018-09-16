<?php

namespace Erfans\AssetBundle\Asset;

use App\Erfans\AssetBundle\Asset\ConfigurableInterface;
use Erfans\AssetBundle\Agents\InstallerInterface;
use Erfans\AssetBundle\Config\AssetConfig;
use Erfans\AssetBundle\Config\AssetManagerConfig;
use Erfans\AssetBundle\DependencyInjection\Configuration;
use Erfans\AssetBundle\Util\FileSystem;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;

class AssetManager implements ConfigurableInterface {

    /** @var string $rootDirectory */
    private $rootDirectory;

    /** @var FileSystem $fileSystem */
    private $fileSystem;

    /** @var ParameterBagInterface $params */
    private $params;

    /** @var AssetManagerConfig $config */
    private $config;

    /** @var array $installers */
    private $installers = [];

    /**
     * Manager constructor.
     *
     * @param                                                                           $rootDirectory
     * @param FileSystem                                                                $fileSystem
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params
     */
    public function __construct($rootDirectory, FileSystem $fileSystem, ParameterBagInterface $params) {
        $this->rootDirectory = $rootDirectory;
        $this->fileSystem = $fileSystem;
        $this->params = $params;
    }

    /**
     * Set a related configuration to the agent from erfans_asset config
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config) {
        $this->config = new AssetManagerConfig($config);
    }

    /**
     * @param InstallerInterface $agent
     * @param                    $alias
     */
    public function addInstaller(InstallerInterface $agent, $alias) {
        $this->installers[$alias] = $agent;
    }

    /**
     * @param $alias
     *
     * @return InstallerInterface
     */
    public function getInstaller($alias) {
        if (!array_key_exists($alias, $this->installers)) {
            throw new \InvalidArgumentException(
                "Agent with alias '$alias' does not found.".
                " Service is not registered or not tagged correctly."
            );
        }

        return $this->installers[$alias];
    }

    /**
     * @return \Symfony\Component\Config\Definition\NodeInterface
     */
    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder('assets');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->beforeNormalization()
            ->always()
            ->then(
                function ($v) {
                    foreach ($v as $key => $value) {
                        if (is_string($key)) {
                            $v[$key]["alias"] = $key;
                        }
                    }

                    return $v;
                }
            )
            ->end()
            ->prototype("array")
            ->children()
            ->scalarNode("alias")->end()
            ->scalarNode("installer")->isRequired()->cannotBeEmpty()->end()
            ->scalarNode("id")->end()
            ->scalarNode("version")->end()
            ->scalarNode("install_directory")->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder->buildTree();
    }

    /**
     * return an array of bundles name which should be included
     *
     * @return array
     */
    public function getBundles() {
        if ($this->config->isAllBundles()) {
            return array_keys($this->params->get('kernel.bundles'));
        } else {
            return $this->config->getBundles();
        }
    }

    /**
     * @param $bundle
     *
     * @return array|null
     * @throws \ReflectionException
     */
    protected function getBundleAssetConfigs($bundle) {
        $filePath = $this->fileSystem->getBundleFile($bundle, '/Resources/config/asset.yml');

        $configTree = $this->getConfigTreeBuilder();

        if (is_file($file = $filePath)) {
            $configArray = Yaml::parse($this->fileSystem->getContent($file));
            $processor = new Processor();
            try {
                return $processor->process($configTree, $configArray);
            } catch (\Exception $ex) {
                throw new \RuntimeException(
                    "Could not process asset config for bundle '$bundle' at '$filePath'.", 500, $ex
                );
            }
        }

        return null;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $config
     *
     * @throws \ReflectionException
     */
    public function install(InputInterface $input, OutputInterface $output, array $config = []) {

        $logger = new ConsoleLogger($output);

        // Create $assetConfigs
        /** @var \Erfans\AssetBundle\Config\AssetConfig[] $assetConfigs */
        $assetConfigs = [];

        $bundles = isset($config["bundles"]) ? $config["bundles"] : $this->getBundles();

        if (is_string($bundles)) {
            $bundles = explode(" ", $bundles);
        }

        foreach ($bundles as $bundle) {
            $assets = $this->getBundleAssetConfigs($bundle);
            if ($assets != null) {
                foreach ($assets as $asset) {
                    // set related bundle to asset config
                    $asset["bundle"] = $bundle;

                    // set output directory
                    $installDirectory = $asset["install_directory"];
                    $installDirectory = isset($installDirectory) ? $installDirectory :
                        $this->config->getAgentDefaultInstallDirectory($asset["agent"]);
                    $installDirectory = isset($installDirectory) ? $installDirectory :
                        $this->config->getDefaultInstallDirectory();

                    // replace bundle variable with bundle directory
                    $installDirectory = str_replace(
                        Configuration::BUNDLE_VARIABLE,
                        $this->fileSystem->getBundleDirectory($bundle),
                        $installDirectory
                    );

                    // convert to absolute path
                    if (!$this->fileSystem->isAbsolutePath($installDirectory)) {
                        $installDirectory = $this->rootDirectory."/../".$installDirectory;
                    }

                    $asset["install_directory"] = $installDirectory;

                    // make asset config object
                    $assetConfig = new AssetConfig($asset);
                    $assetConfigs[] = $assetConfig;
                }
            }
        }

        $agentConfigs = [];

        /**
         * Separate the asset configs based on installer
         *
         * @var AssetConfig $assetConfig
         */
        foreach ($assetConfigs as $assetConfig) {
            $agentConfigs [$assetConfig->getInstaller()][] = $assetConfig;
        }

        /**
         * @var string        $agent
         * @var AssetConfig[] $config
         */
        foreach ($agentConfigs as $agent => $config) {
            $logger->info("Start installing by '".$agent."'");

            /** @var InstallerInterface $agentService */
            $agentService = $this->getInstaller($agent);

            $agentService->setLogger($logger);
            $agentService->setConsoleInterface($input, $output);

            try {
                $agentService->install($config);
            } catch (\Exception $ex) {
                $logger->error("An error occurred while '$agent' tries to install");

                throw new \RuntimeException($ex);
            }
        }
    }
}
