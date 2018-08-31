<?php

namespace Erfans\AssetBundle\Asset;

use Erfans\AssetBundle\Agents\InstallerInterface;
use Erfans\AssetBundle\Model\AssetConfig;
use Erfans\AssetBundle\Util\PathUtil;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;

class AssetManager {

    /** @var PathUtil $pathUtil */
    private $pathUtil;

    /** @var ParameterBagInterface $params */
    private $params;

    /** @var Config $config */
    private $config;

    /** @var array $installers */
    private $installers = [];

    /**
     * Manager constructor.
     *
     * @param PathUtil                                                                  $pathUtil
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params
     * @param Config                                                                    $config
     */
    public function __construct(PathUtil $pathUtil, ParameterBagInterface $params, Config $config) {
        $this->pathUtil = $pathUtil;
        $this->params = $params;
        $this->config = $config;
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
            ->arrayNode("main_files")->prototype("scalar")->end()->end()
            ->scalarNode("output_directory")->end()
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
        $filePath = $this->pathUtil->getBundleFile($bundle, '/Resources/config/asset.yml');

        $configTree = $this->getConfigTreeBuilder();

        if (is_file($file = $filePath)) {
            $configArray = Yaml::parse($this->pathUtil->getContent($file));
            $processor = new Processor();
            try {
                $config = $processor->process($configTree, $configArray);

                xdebug_break();
                foreach ($config as $key => $value) {
                    if (isset($value["output_directory"])) {
                        $value["output_directory"] = str_replace(
                            '%bundle%',
                            $this->pathUtil->getBundleDirectory($bundle),
                            $value["output_directory"]
                        );

                        $config[$key] = $value;
                    }
                }

                return $config;
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
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     * @throws \ReflectionException
     */
    public function install(InputInterface $input, OutputInterface $output, array $config = []) {
        // Create $assetConfigs
        /** @var \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs */
        $assetConfigs = [];

        $bundles = isset($config["bundles"]) ? $config["bundles"] : $this->getBundles();
        foreach ($bundles as $bundle) {
            $assets = $this->getBundleAssetConfigs($bundle);
            if ($assets != null) {
                foreach ($assets as $asset) {
                    $asset["bundle"] = $bundle;
                    $assetConfig = new AssetConfig($asset);
                    $assetConfigs[] = $assetConfig;
                }
            }
        }

        return $this->doInstall($assetConfigs, $input, $output);
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface                          $input
     * @param OutputInterface                         $output
     *
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    protected function doInstall(array $assetConfigs, InputInterface $input, OutputInterface $output) {
        $configs = [];
        foreach ($assetConfigs as $assetConfig) {
            $configs [$assetConfig->getInstaller()][] = $assetConfig;
        }

        $assetConfigs = [];

        foreach ($configs as $agent => $config) {
            $output->writeln("Start installing by '".$agent."'");

            $agentService = $this->getInstaller($agent);

            try {
                $agentService->setInputInterface($input);
                $agentService->setOutputInterface($output);

                $assetConfig = $agentService->install($config);
            } catch (\Exception $ex) {
                $output->writeln("An error occurred while '$agent' tries to install");

                throw new \RuntimeException($ex);
            }

            $assetConfigs = array_merge($assetConfigs, $assetConfig);
        }

        return $assetConfigs;
    }
}
