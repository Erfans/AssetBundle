<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/2/2016
 * Time: 20:12
 */

namespace Erfans\AssetBundle\Asset;

use Erfans\AssetBundle\Agents\InstallerInterface;
use Erfans\AssetBundle\Model\AssetConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Output\OutputInterface;

class AssetManager
{
    /** @var ContainerInterface $container */
    private $container;

    /** @var Config $config */
    private $config;

    /** @var array $installers */
    private $installers = [];

    /**
     * Manager constructor.
     *
     * @param ContainerInterface $container
     * @param Config $config
     */
    public function __construct(ContainerInterface $container, Config $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * @param InstallerInterface $agent
     * @param $alias
     */
    public function addInstaller(InstallerInterface $agent, $alias)
    {
        $this->installers[$alias] = $agent;
    }

    /**
     * @param $alias
     * @return InstallerInterface
     */
    public function getInstaller($alias)
    {
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
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('assets');

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
    public function getBundles()
    {
        if ($this->config->isAllBundles()) {
            return array_keys($this->container->getParameter('kernel.bundles'));
        } else {
            return $this->config->getBundles();
        }
    }

    /**
     * get path of bundle main file
     *
     * @param $bundle
     * @return string
     */
    protected function getBundlePath($bundle)
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!array_key_exists($bundle, $bundles)) {
            throw new \InvalidArgumentException("Bundle '$bundle' not found in registered bundles.");
        }

        return $this->container->getParameter('kernel.bundles')[$bundle];
    }

    /**
     * @param $bundle
     * @return array|null
     */
    protected function getBundleAssetConfigs($bundle)
    {
        $bundlePath = $this->getBundlePath($bundle);
        $reflection = new \ReflectionClass($bundlePath);

        $configTree = $this->getConfigTreeBuilder();

        $fileAddress = dirname($reflection->getFilename()).'/Resources/config/asset.yml';

        if (is_file($file = $fileAddress)) {
            $configArray = Yaml::parse(file_get_contents(realpath($file)));
            $processor = new Processor();
            try {
                return $processor->process($configTree, $configArray);
            } catch (\Exception $ex) {
                throw new \RuntimeException(
                    "Could not process asset config for bundle '$bundle' at '$fileAddress'.",
                    500,
                    $ex
                );
            }
        }

        return null;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(InputInterface $input, OutputInterface $output)
    {
        // Create $assetConfigs
        /** @var \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs */
        $assetConfigs = [];

        $bundles = $this->getBundles();
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    protected function doInstall(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        $configs = [];
        foreach ($assetConfigs as $assetConfig) {
            $configs [$assetConfig->getInstaller()][] = $assetConfig;
        }

        $assetConfigs = [];

        foreach ($configs as $agent => $config) {
            $output->writeln("Start installing by '".$agent."'");

            $agentService = $this->getInstaller($agent);

            try {
                $assetConfig = $agentService->install($config, $input, $output);
            } catch (\Exception $ex) {
                $output->writeln("An error occurred while '$agent' tries to install");

                throw new \RuntimeException($ex);
            }

            $assetConfigs = array_merge($assetConfigs, $assetConfig);
        }

        return $assetConfigs;
    }

}