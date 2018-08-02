<?php

namespace Erfans\AssetBundle\Agents\Null;


use Erfans\AssetBundle\Agents\InstallerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This agent do nothing and is made for test purposes
 *
 * Class NullAgent
 * @package Erfans\AssetBundle\Agents\Null
 */
class NullAgent implements InstallerInterface
{
    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs)
    {
        return $assetConfigs;
    }

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function setOutputInterface(OutputInterface $output)
    {
    }

    /**
     * @param InputInterface $input
     * @return void
     */
    public function setInputInterface(InputInterface $input)
    {
    }
}
