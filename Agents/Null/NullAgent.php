<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/7/2016
 * Time: 16:46
 */

namespace Erfans\AssetBundle\Agents\Null;


use Erfans\AssetBundle\Agents\DownloadAgentInterface;
use Erfans\AssetBundle\Agents\OptimizeAgentInterface;
use Erfans\AssetBundle\Agents\ReferenceAgentInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NullAgent implements DownloadAgentInterface, ReferenceAgentInterface, OptimizeAgentInterface
{
    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function download(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        return $assetConfigs;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function optimize(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        return $assetConfigs;
    }

    /**
     * @param \Erfans\AssetBundle\Model\AssetConfig[] $assetConfigs
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Erfans\AssetBundle\Model\AssetConfig[] assetConfigs
     */
    public function reference(array $assetConfigs, InputInterface $input, OutputInterface $output)
    {
        return $assetConfigs;
    }
}