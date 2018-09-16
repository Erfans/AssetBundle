<?php

namespace Erfans\AssetBundle\DependencyInjection;

use App\Erfans\AssetBundle\Asset\ConfigurableInterface;
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
class ErfansAssetExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {

        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $agents = array_key_exists("agents", $config) ? $config["agents"] : [];

        $definition = $container->findDefinition('erfans_asset.asset_manager');
        $definition->addMethodCall("setConfig", [$config]);

        $taggedServices = $container->findTaggedServiceIds('erfans_asset.agent');
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {

                $alias = $attributes["alias"];
                $definition->addMethodCall('addInstaller', [new Reference($id), $alias]);

                if (array_key_exists($alias, $agents)) {
                    $agentConfig = $agents[$alias];

                    $agentDefinition = $container->getDefinition($id);

                    $agentClass = $agentDefinition->getClass();
                    if (is_a($agentClass, ConfigurableInterface::class, true)) {
                        $agentDefinition->addMethodCall("setConfig", [$agentConfig]);
                    }
                }
            }
        }
    }
}
