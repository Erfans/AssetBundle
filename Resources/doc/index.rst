This bundle purpose is to manage third party assets. As it is
supposed to not adding third party assets to your public bundles and
still your bundle may be relied on them alternatively you can only add
information of desired third party assets to the bundle and in final
product developer can install them. It is possible to define multiple agents
(downloader or installer) service and tag them with erfans_asset.agent
and add alias attribute to use them in installing process. In asset
config files you can define different agent for each asset.
By now only Bower agent has implemented in this bundle.

Original tendency to develop this bundle was to have Mapping (for example
to wire up installed assets to RequireJs or to twig by assets alias)
and Optimizing step additionally to Downloading, but for now I have skipped
them.

Installation
Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

$ composer require erfans/asset-bundle "~1.2@dev"
This command requires you to have Composer installed globally, as explained
in the installation chapter
of the Composer documentation.

Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the app/AppKernel.php file of your project:

<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Erfans\AssetBundle\ErfansAssetBundle(),
        );

        // ...
    }

    // ...
}
Step 3: Configuration

Default configuration for "ErfansAssetBundle":

#app\config\config.yml
erfans_asset:
    enabled:              false

    # Consider all bundles to check their asset config.
    all_bundles:          false

    # Bundles to check their asset config file.
    bundles:

        # Default:
        - AppBundle

    # Available agents by this bundle.
    agents:

        # File agent to install assets by file url
        file:
            # Install directory
            directory: "web/vendor"

            # Create directory per asset
            create_directory: true

        # Bower agent to download defined assets.
        bower:

            # Directory path to cache assets before installing. You need to change it if you use Symfony2.
            cache_path:           '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%'

            # Install directory of assets. default directory is 'web/bower_components'.
            directory:            web/bower_components

            # Github token to extend limitation of 60 repository per hour to 5000.
            github_token:         ~

            # <https://github.com/bower/spec/blob/master/json.md>
            package:

                # The name of the package as stored in the registry.
                # <https://github.com/bower/spec/blob/master/json.md#name>
                name:                 application

                # Help users identify and search for your package with a brief description. Describe what your package does, rather than what it's made of. Will be displayed in search/lookup results on the CLI and the website that can be used to search for packages.
                description:          ~

                # The entry-point files necessary to use your package. Only one file per filetype.
                main:                 []

                # The type of module defined in the main JavaScript file. Can be one or an array of the following strings:
                # <https://github.com/bower/spec/blob/master/json.md#moduletype>
                module_type:          []

                # <https://github.com/bower/spec/blob/master/json.md#license>
                license:              []

                # A list of files for Bower to ignore when installing your package.
                ignore:               []

                # Same format requirements as name. Used for search by keyword. Helps make your package easier to discover without people needing to know its name.
                keywords:             []

                # A list of people that authored the contents of the package.
                authors:              []

                # URL to learn more about the package. Falls back to GitHub project if not specified and it’s a GitHub endpoint.
                homepage:             ~

                # The repository in which the source code can be found.
                repository:           ~

                # Dependencies are specified with a simple hash of package name to a  server compatible identifier or URL.
                # <https://github.com/bower/spec/blob/master/json.md#dependencies>
                # It is recommended to use bundle asset config file instead of global dependencies, you can set more configuration in bundle asset config file
                dependencies:         ~

                    # Examples:
                    jquery:              2.2.4
                    bootstrap-sass:      3.3.6

                # Dependencies that are only needed for development of the package, e.g., test framework or building documentation.
                dev_dependencies:     ~

                    # Examples:
                    jquery:              2.2.4
                    bootstrap-sass:      3.3.6

                # Dependency versions to automatically resolve with if conflicts occur between packages.
                resolutions:          ~

                    # Example:
                    angular:             1.3.0-beta.16

                # If set to true, Bower will refuse to publish it. This is a way to prevent accidental publication of private repositories.
                private:              true

            # <https://github.com/bower/spec/blob/master/config.md>
            bower:

                # The registry config. Can be an object or a string. If a string is used, all the property values below will have its value. Defaults to the bower registry URL.
                registry:

                    # An array of URLs pointing to read-only Bower registries. A string means only one. When looking into the registry for an endpoint, Bower will query these registries by the specified order.
                    search:               []

                    # The URL to use when registering packages.
                    register:             ~

                    # The URL to use when publishing packages.
                    publish:              ~

                # Define a custom template for shorthand package names.
                # <https://github.com/bower/spec/blob/master/config.md#shorthand-resolver>
                shorthand_resolver:   ~

                # The proxy to use for http requests.
                proxy:                ~

                # The proxy to use for https requests.
                https_proxy:          ~

                # Sets the User-Agent for each request made.<https://github.com/bower/spec/blob/master/config.md#user-agent>
                user_agent:           ~

                # The timeout to be used when making requests in milliseconds, defaults to 60000 ms.
                timeout:              ~

                # Whether or not to do SSL key validation when making requests via https.
                strict_ssl:           ~

                # The CA certificates to be used, defaults to null. This is similar to the registry key, specifying each CA to use for each registry endpoint.<https://github.com/bower/spec/blob/master/config.md#ca>
                ca:                   ~

                # Enable or disable use of colors in the CLI output. Defaults to true.
                color:                ~

                # Where to store persistent data, such as cache, needed by bower.Defaults to paths that suit the OS/platform. <https://github.com/bower/spec/blob/master/config.md#storage>
                storage:
                    packages:             '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/packages'
                    registry:             '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/registry'
                    links:                '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/links'

                # Where to store temporary files and folders.  Defaults to the system temporary directory suffixed with /bower.
                tmp:                  '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%/tmp'

                # Makes bower interactive, prompting whenever necessary.
                interactive:          ~

                # List of Pluggable Resolvers to use for locating and fetching packages.<https://github.com/bower/spec/blob/master/config.md#resolvers>
                resolvers:            []

                # Bower's default behavior is to not use shallow cloning, since some Git hosts fail to provide a response when asked to do a shallow clone<https://github.com/bower/spec/blob/master/config.md#shallowclonehosts>
                shallow_clone_hosts:  []

                # Bower provides 3 separate hooks that can be used to trigger other automated tools during Bower usage.<https://github.com/bower/spec/blob/master/config.md#scripts>
                scripts:
                    preinstall:           ~
                    postinstall:          ~
                    preuninstall:         ~

                # Bower will ignore these dependencies when resolving packages.
                ignored_dependencies:  []
It is long configuration to customize bower agent, however, usual necessary configuration is:

#app\config\config.yml
erfans_asset:
    all_bundles: true

    agents:
        file:
            directory: "web/target-folder"
            create_directory: true
        bower:
            directory: "web/target-folder"
            github_token: github_token_to_extend_limitation
Note that if you use Symfony2 you need to change cash directory in configuration.

Step 3: Add bundle asset config file

To define required third party asset for each bundle create asset.yml file
in Resources/config directory of bundle.

#AppBundle\Resources\config\asset.yml
assets:
    jquery: # alias of asset
        installer: bower # name of installer, you can also define your own installer
        id: jquery       # id of repository which passes to installer
        version: ~1.9    # version of repository

    jquery_easing:
        installer: file
        id: http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js
Step 4: Install assets

To download and copy defined assets to target folder run command erfans:asset:install
by Symfony console.

This bundle uses bowerphp. Since this library does not support downloading files by url I added a file installer which download asset files and put them in final directory.

Step 5: Add assets to frontend

Now you can add installed assets to your twig or other asset loaders such
as RequireJs.