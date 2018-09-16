<?php

namespace Erfans\AssetBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{

    const BUNDLE_VARIABLE = "?bundle";

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $bundleVariable = self::BUNDLE_VARIABLE;

        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('erfans_asset');

        $rootNode
            ->children()
                ->booleanNode("enabled")->defaultFalse()->end()
                ->booleanNode("all_bundles")->info("Consider all bundles to check their asset config.")->defaultFalse()->end()
                ->arrayNode("bundles")
                    ->info("Bundles to check their asset config file.")
                    ->prototype("scalar")->end()
                    ->defaultValue(["AppBundle"])
                ->end()
                ->scalarNode("default_output_directory")
                    ->info("The default output directory. The default value us $bundleVariable/Resources/public; $bundleVariable will be replaced with the bundle directory.")
                    ->defaultValue("$bundleVariable/Resources/public")
                ->end()
                ->arrayNode("agents")
                    ->info("Configurations to pass to each agent by their alias.")
                    ->children()
                        ->append($this->getFileConfig())
                        ->append($this->getBowerConfig())
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function getFileConfig()
    {
        $bundleVariable = self::BUNDLE_VARIABLE;

        $treeBuilder = new TreeBuilder();
        $fileNode = $treeBuilder->root('file');

        $fileNode
            ->info("File agent to download defined assets by url.")
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode("default_output_directory")
                    ->cannotBeEmpty()
                    ->info("Default directory to install assets. default directory is '$bundleVariable/Resources/public'.")
                    ->defaultValue("$bundleVariable/Resources/public")
                ->end()
                ->booleanNode("create_directory")
                    ->defaultTrue()
                    ->info("Create a new directory for each file with name of alias in download directory.")
                ->end()
            ->end();

        return $fileNode;
    }

    private function getBowerConfig()
    {
        $bundleVariable = self::BUNDLE_VARIABLE;

        $treeBuilder = new TreeBuilder();
        $bowerNode = $treeBuilder->root('bower');

        $bowerNode
            ->info("Bower agent to download defined assets.")
            ->children()
                ->scalarNode("cache_path")
                    ->cannotBeEmpty()
                    ->defaultValue("%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%")
                    ->info("Directory path to cache assets before installing. You need to change it if you use Symfony2.")
                ->end()
                ->scalarNode("default_output_directory")
                    ->cannotBeEmpty()
                    ->info("Default directory to install assets. The default value is '$bundleVariable/Resources/public'.")
                    ->defaultValue("$bundleVariable/Resources/public")
                    ->end()
                ->scalarNode("github_token")
                    ->info("Github token to extend limitation of 60 repository per hour to 5000.")
                ->end()
                ->append($this->getBowerEnvironmentConfig())
            ->end();

        return $bowerNode;
    }

    private function getBowerEnvironmentConfig()
    {
        $treeBuilder = new TreeBuilder();
        $bowerNode = $treeBuilder->root('bower');

        $bowerNode
            ->info("<https://github.com/bower/spec/blob/master/config.md>")
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode("registry")
                    ->info("The registry config. Can be an object or a string.".
                        " If a string is used, all the property values below will have its value.".
                        " Defaults to the bower registry URL.")
                    ->children()
                        ->arrayNode("search")
                            ->info("An array of URLs pointing to read-only Bower registries. A string means only one.".
                                " When looking into the registry for an endpoint, ".
                                "Bower will query these registries by the specified order.")
                            ->beforeNormalization()
                            ->ifString()
                            ->then(
                                function ($v) {
                                    return [$v];
                                }
                            )
                            ->end()
                            ->prototype("scalar")->end()
                        ->end()
                        ->scalarNode("register")
                            ->info("The URL to use when registering packages.")
                            ->end()
                        ->scalarNode("publish")
                            ->info("The URL to use when publishing packages.")
                            ->end()
                        ->end()
                    ->end()
                ->scalarNode("shorthand_resolver")
                    ->info("Define a custom template for shorthand package names. \n".
                    "<https://github.com/bower/spec/blob/master/config.md#shorthand-resolver>")
                    ->end()
                ->scalarNode("proxy")
                    ->info("The proxy to use for http requests.")
                    ->end()
                ->scalarNode("https_proxy")
                    ->info("The proxy to use for https requests.")
                    ->end()
                ->scalarNode("user_agent")
                    ->info("Sets the User-Agent for each request made.".
                        "<https://github.com/bower/spec/blob/master/config.md#user-agent>")
                    ->end()
                ->integerNode("timeout")
                    ->info("The timeout to be used when making requests in milliseconds, defaults to 60000 ms.")
                    ->end()
                ->booleanNode("strict_ssl")
                    ->info("Whether or not to do SSL key validation when making requests via https.")
                    ->end()
                ->variableNode("ca")
                    ->info("The CA certificates to be used, defaults to null.".
                        " This is similar to the registry key, specifying each CA to use for each registry endpoint.".
                        "<https://github.com/bower/spec/blob/master/config.md#ca>")
                    ->end()
                ->booleanNode("color")
                    ->info("Enable or disable use of colors in the CLI output. Defaults to true.")
                    ->end()
                ->arrayNode("storage")
                    ->info("Where to store persistent data, such as cache, needed by bower.".
                    "Defaults to paths that suit the OS/platform. ".
                    "<https://github.com/bower/spec/blob/master/config.md#storage>")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode("packages")
                            ->defaultValue("%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/packages")
                            ->end()
                        ->scalarNode("registry")
                            ->defaultValue("%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/registry")
                            ->end()
                        ->scalarNode("links")
                            ->defaultValue("%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/links")
                            ->end()
                        ->end()
                    ->end()
                ->scalarNode("tmp")
                    ->info("Where to store temporary files and folders. ".
                        " Defaults to the system temporary directory suffixed with /bower.")
                    ->defaultValue("%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/tmp")
                    ->end()
                ->booleanNode("interactive")
                    ->info("Makes bower interactive, prompting whenever necessary.")
                    ->end()
                ->arrayNode("resolvers")
                    ->info("List of Pluggable Resolvers to use for locating and fetching packages.".
                        "<https://github.com/bower/spec/blob/master/config.md#resolvers>")
                    ->prototype("scalar")->end()
                    ->end()
                ->arrayNode("shallow_clone_hosts")
                    ->info("Bower's default behavior is to not use shallow cloning, since some Git hosts ".
                        "fail to provide a response when asked to do a shallow clone".
                        "<https://github.com/bower/spec/blob/master/config.md#shallowclonehosts>")
                    ->prototype("scalar")->end()
                    ->end()
                ->arrayNode("scripts")
                    ->info("Bower provides 3 separate hooks that can be used to ".
                        "trigger other automated tools during Bower usage.".
                        "<https://github.com/bower/spec/blob/master/config.md#scripts>")
                    ->children()
                    ->scalarNode("preinstall")->end()
                    ->scalarNode("postinstall")->end()
                    ->scalarNode("preuninstall")->end()
                    ->end()
                ->end()
                ->arrayNode("ignored_dependencies")
                    ->info("Bower will ignore these dependencies when resolving packages.")
                    ->prototype("scalar")->end()
                    ->end()
            ->end();

        return $bowerNode;
    }
}
