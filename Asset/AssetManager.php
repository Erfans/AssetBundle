<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/2/2016
 * Time: 20:12
 */

namespace Erfans\AssetBundle\Asset;


use Erfans\AssetBundle\Agents\AgentInterface;
use Erfans\AssetBundle\Agents\DownloadAgentInterface;
use Erfans\AssetBundle\Agents\OptimizeAgentInterface;
use Erfans\AssetBundle\Agents\ReferenceAgentInterface;
use Erfans\AssetBundle\Model\AssetConfig;
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

    /** @var array $agents */
    private $agents = [];

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
     * @param AgentInterface $agent
     * @param $alias
     */
    public function addAgent(AgentInterface $agent, $alias)
    {
        $this->agents[$alias] = $agent;
    }

    /**
     * @param $alias
     * @return AgentInterface
     */
    public function getAgent($alias)
    {
        if (!array_key_exists($alias, $this->agents)) {
            throw new \InvalidArgumentException(
                "Agent with alias '$alias' does not found.".
                " Service is not registered or not tagged correctly."
            );
        }

        return $this->agents[$alias];
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
    private function getBundlePath($bundle)
    {
        $bundles = $this->container->getParameter('kernel.bundles');

        if (!array_key_exists($bundle, $bundles)) {
            throw new \InvalidArgumentException("Bundle '$bundle' not found in registered bundles.");
        }

        return $this->container->getParameter('kernel.bundles')[$bundle];
    }

    /**
     * get asset config for passed bundle
     *
     * @param $bundle
     * @return array|null
     */
    private function getAssetConfig($bundle)
    {
        $bundlePath = $this->getBundlePath($bundle);
        $reflection = new \ReflectionClass($bundlePath);
        if (is_file($file = dirname($reflection->getFilename()).'/Resources/config/asset.yml')) {
            return Yaml::parse(file_get_contents(realpath($file)));
        }

        return null;
    }

    /**
     * @param array $config
     * @return \Erfans\AssetBundle\Model\AssetConfig assetConfig
     */
    private function createAssetConfig(array $config)
    {
        $assetConfig = new AssetConfig();

        if (key_exists("id", $config)) {
            $assetConfig->setId($config["id"]);
        }

        if (key_exists("version", $config)) {
            $assetConfig->setVersion($config["version"]);
        }

        if (key_exists("alias", $config)) {
            $assetConfig->setAlias($config["alias"]);
        }

        if (key_exists("main_files", $config)) {
            $assetConfig->setAlias($config["main_files"]);
        }

        return $assetConfig;
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

        // TODO: add config checker for loaded files
        $bundles = $this->getBundles();
        foreach ($bundles as $bundle) {
            $config = $this->getAssetConfig($bundle);
            if ($config != null) {
                $assets = $config["assets"];
                foreach ($assets as $asset) {
                    // TODO: Check conflict before adding
                    $assetConfig = $this->createAssetConfig($asset);
                    $assetConfig->setBundle($bundle);
                    $assetConfigs[] = $assetConfig;
                }
            }
        }

        $assetConfigs = $this->download($assetConfigs, $input, $output);
        $assetConfigs = $this->reference($assetConfigs, $input, $output);
        $assetConfigs = $this->optimize($assetConfigs, $input, $output);

        return $assetConfigs;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function download(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        $agents = $this->config->getDownloadAgent();

        foreach ($agents as $agent) {
            $output->writeln("Start downloading by '".$agent."'");
            /** @var DownloadAgentInterface $agentService */
            $agentService = $this->getAgent($agent);
            $assetConfigs = $agentService->download($assetConfigs, $input, $output);
        }

        return $assetConfigs;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function reference(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        $agents = $this->config->getReferenceAgent();

        foreach ($agents as $agent) {
            $output->writeln("Start referencing by '".$agent."'");
            /** @var ReferenceAgentInterface $agentService */
            $agentService = $this->getAgent($agent);
            $assetConfigs = $agentService->reference($assetConfigs, $input, $output);
        }

        return $assetConfigs;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function optimize(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        $agents = $this->config->getOptimizeAgent();

        foreach ($agents as $agent) {
            $output->writeln("Start optimizing by '".$agent."'");
            /** @var OptimizeAgentInterface $agentService */
            $agentService = $this->getAgent($agent);
            $assetConfigs = $agentService->optimize($assetConfigs, $input, $output);
        }

        return $assetConfigs;
    }
}