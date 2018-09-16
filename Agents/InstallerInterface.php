<?php

namespace Erfans\AssetBundle\Agents;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface InstallerInterface {

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function setConsoleInterface(InputInterface $input, OutputInterface $output);

    /**
     * @param \Erfans\AssetBundle\Config\AssetConfig[] $assetConfigs
     *
     * @return \Erfans\AssetBundle\Config\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs);
}
