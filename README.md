The purpose of this bundle is to manage third party assets for reusable bundles.

Motivation
==========
As it is mentioned in Symfony 
[documentation](https://symfony.com/doc/master/bundles/best_practices.html#vendors):
> A bundle should also not embed third-party libraries written in JavaScript, CSS or any other language.

Additionally due to license restrictions and conflicts a bundle may could 
not include third party assets 
(e.g. [FOSCKEditorBundle](http://symfony.com/doc/master/bundles/FOSCKEditorBundle/usage/ckeditor.html)).

To solve this situation, a bundle could only contain a configuration and 
by running a command line this bundle will install the configured assets. 

This bundle allows to define multiple agents (downloader or installer) 
service and tag them with `erfans_asset.agent`
and add `alias` attribute to use them in the installing process. 
Bundles can contain asset config files to install assets by proper agent.
(By now only Bower and File agents have been implemented.) 

Installation
============

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require erfans/asset-bundle
```

Applications that don't use Symfony Flex
----------------------------------------
### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require erfans/asset-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
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
```

Step 3: Configuration
---------------------
Default configuration for "ErfansAssetBundle":
```Yaml
#app\config\config.yml or
#app\packages\erfans_asset.yml

erfans_asset:
    enabled:              false

    # Consider all bundles to check their asset config.
    all_bundles:          false

    # Bundles to check their asset config file.
    bundles:

        # Default:
        - AppBundle

    # The default install directory. The default value is ?bundle/Resources/public; ?bundle will be replaced with the bundle directory.
    default_install_directory: '?bundle/Resources/public'

    # Configurations to pass to each agent by their alias.
    agents:

        # File agent to download defined assets by url.
        file:

            # Default directory to install assets. default directory is '?bundle/Resources/public'.
            default_install_directory: '?bundle/Resources/public'

            # Create a new directory for each file with name of alias in download directory.
            create_directory:     true

        # Bower agent to download defined assets.
        bower:

            # Directory path to cache assets before installing. You need to change it if you use Symfony2.
            cache_path:           '%kernel.root_dir%/../var/erfans_asset/bower_cache/%kernel.environment%'

            # Default directory to install assets. The default value is '?bundle/Resources/public'.
            default_install_directory: '?bundle/Resources/public'

            # Github token to extend limitation of 60 repository per hour to 5000.
            github_token:         ~
```        

However, the usual necessary configurations are:
```Yaml
#app\config\config.yml or
#app\packages\erfans_asset.yml

erfans_asset:
    all_bundles: true
    default_install_directory: '?bundle/Resources/public'

    agents:
        file:
            create_directory: true
            
        bower:
            github_token: github_token_to_extend_limitation
```

Please note, if you use Symfony2 you need to change the cash directory for bower configuration.

Step 3: Add asset config file
---------------------
To define required third party assets for each bundle create an `asset.yml` file 
in `Resources/config` directory of bundle.
 
```Yaml
#AppBundle\Resources\config\asset.yml
assets:
    jquery: # alias of asset
        installer: bower # name of installer
        id: jquery       # id of repository which passes to the installer
        version: ~1.9    # version of repository
        
    jquery_easing:
        installer: file
        id: http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js        
```        

Step 4: Install assets
---------------------
To download and copy defined assets to the target folder run command `erfans:asset:install` 
in Symfony console. You can limit the installing assets to specific bundles, by passing the
bundle names as arguments, e.g. `erfans:asset:install AppBundle`.   

The bower agent of this bundle is based on [bowerphp](https://bowerphp.org/ "Bee-Lab/bowerphp") 
which does not currently support the downloading assets by url.
Therefore, a file installer is added to the bundle to download remote files and put them in the target directory.  

Step 5: Add assets to frontend
------------------------------
Now you can add the installed assets to your twig or other asset loaders such 
as RequireJs. If you used the public directory of a bundle as install directory,
then you need also to transfer them to public folder by `assets:install` command.
