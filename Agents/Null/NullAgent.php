<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/7/2016
 * Time: 16:46
 */

namespace Erfans\AssetBundle\Agents\Null;


use Erfans\AssetBundle\Agents\InstallerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This agent do nothing and its for test purposes
 *
 * Class NullAgent
 * @package Erfans\AssetBundle\Agents\Null
 */
class NullAgent implements InstallerInterface
{
    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function install(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        return $assetConfigs;
    }

}