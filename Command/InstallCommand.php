<?php

namespace Erfans\AssetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('erfans:asset:install')
            ->setDescription('Install assets defined in asset.yml');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $assetManager = $this->getContainer()->get("erfans_asset.asset_manager");
        $output->writeln("Start installing...");
        $assetManager->install($input, $output);
    }
}
