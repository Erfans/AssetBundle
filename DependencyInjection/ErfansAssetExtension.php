<?php

namespace Erfans\AssetBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ErfansAssetExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $definition = $container->findDefinition('erfans_asset.asset_manager');
        $taggedServices = $container->findTaggedServiceIds('erfans_asset.agent');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addAgent', array(new Reference($id), $attributes["alias"]));
            }
        }

        if (array_key_exists("agents", $config) && array_key_exists("bower", $config["agents"])) {
            $definition = $container->getDefinition("erfans_asset.agents.bower");
            $definition->addArgument($config["agents"]["bower"]);
        }

        if (array_key_exists("agents", $config) && array_key_exists("requirejs", $config["agents"])) {
//            $definition = $container->getDefinition("erfans_asset.agents.requirejs");
//            $definition->addArgument($config["agents"]["requirejs"]);
        }

        unset($config["agents"]);

        $definition = $container->getDefinition("erfans_asset.config");
        $definition->addArgument($config);
    }
}
