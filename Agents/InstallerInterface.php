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
     * @param OutputInterface $output
     * @return void
     */
    public function setOutputInterface(OutputInterface $output);

    /**
     * @param InputInterface $input
     * @return void
     */
    public function setInputInterface(InputInterface $input);

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs);

}