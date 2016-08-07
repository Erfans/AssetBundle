<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/5/2016
 * Time: 14:22
 */

namespace Erfans\AssetBundle\Agents;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface InstallerInterface
{
    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs, InputInterface $input, OutputInterface $output);

}