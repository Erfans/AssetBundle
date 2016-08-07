<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/13/2016
 * Time: 16:41
 */

namespace Erfans\AssetBundle\Agents;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BaseAgent
 * @package Erfans\AssetBundle\Agents
 *
 * Holds common tools of agents
 */
abstract class BaseAgent
{
    /**
     * Convert keys in an array to values of other array
     *
     * @param array $config
     * @param array $map
     * @return array
     */
    protected function normalizeConfig(array $config = [], array $map)
    {
        // map config keys
        foreach ($config as $key => $value) {
            if (array_key_exists($key, $map)) {
                if (is_array($map[$key])) {
                    $config[$key] = $this->normalizeConfig($config[$key], $map[$key]);
                } else {
                    $config[$map[$key]] = $config[$key];
                    unset($config[$key]);
                }
            }
        }

        return $config;
    }

    /**
     * dump files with output notification and error handling
     *
     * @param $path
     * @param $content
     * @param string $errorMessage
     * @param OutputInterface|null $output
     */
    protected function dumpFile(
        $path,
        $content,
        $errorMessage = "An error occurred while creating file.",
        OutputInterface $output = null
    ) {
        $fileSystem = new FileSystem();

        if ($output != null) {
            $output->writeln("Creating file at ".$path);
        }
        try {
            $fileSystem->dumpFile($path, $content);
        } catch (IOException $e) {
            throw new \RuntimeException($errorMessage." file path: ".$e->getPath());
        }
    }


    protected function mkdir(
        $path,
        $errorMessage = "An error occurred while creating folder.",
        OutputInterface $output = null
    ) {
        $fileSystem = new FileSystem();

        if ($output != null) {
            $output->writeln("Creating file at ".$path);
        }
        try {
            $fileSystem->mkdir($path);
        } catch (IOException $e) {
            throw new \RuntimeException($errorMessage." folder path: ".$e->getPath());
        }
    }


    /**
     * @param array $a
     * @return string
     */
    protected function convertArrayToJsonObject(array $a)
    {
        $options = count($a) == 0 ? JSON_FORCE_OBJECT | JSON_PRETTY_PRINT : JSON_PRETTY_PRINT;

        return json_encode($a, $options);
    }


}