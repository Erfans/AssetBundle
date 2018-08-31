<?php

namespace Erfans\AssetBundle\Command;

use Erfans\AssetBundle\Asset\AssetManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command {

    /** @var \Erfans\AssetBundle\Asset\AssetManager $assetManager */
    private $assetManager;

    /**
     * InstallCommand constructor.
     *
     * @param \Erfans\AssetBundle\Asset\AssetManager $assetManager
     */
    public function __construct(AssetManager $assetManager) {
        parent::__construct();

        $this->assetManager = $assetManager;
    }

    protected function configure() {
        $this
            ->setName('erfans:asset:install')
            ->setDescription('Install the defined assets in asset.yml')
            ->addArgument(
                'bundles',
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Installing assets for this bundle'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln("Start installing...");

        $config = [];

        $bundles = $input->getArgument('bundles');
        if ($bundles && !empty($bundles)) {
            $config['bundles'] = $bundles;
        }

        $this->assetManager->install($input, $output, $config);
    }
}
