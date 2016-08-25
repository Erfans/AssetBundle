<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/13/2016
 * Time: 16:41
 */

namespace Erfans\AssetBundle\Agents;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class BaseAgent
 * @package Erfans\AssetBundle\Agents
 *
 * Holds common tools of agents
 */
abstract class BaseAgent implements InstallerInterface
{

    /** @var OutputInterface $output */
    protected $output;

    /** @var  InputInterface $input */
    protected $input;

    /**
     * @param OutputInterface $output
     * @return void
     */
    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * @param InputInterface $input
     * @return void
     */
    public function setInputInterface(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * @param $message
     * @param string $type
     */
    protected function log($message, $type = null)
    {
        if ($this->getOutput()) {

            $typeOpen = $type ? "<$type>" : "";
            $typeClose = $type ? "</$type>" : "";

            $this->getOutput()->writeln($typeOpen.$message.$typeClose);
        }
    }

    /**
     * @param $message
     */
    protected function logInfo($message)
    {
        return $this->log($message, "info");
    }

    /**
     * @param $message
     */
    protected function logQuestion($message)
    {
        return $this->log($message, "question");
    }

    /**
     * @param $message
     */
    protected function logError($message)
    {
        return $this->log($message, "error");
    }

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
     */
    protected function dumpFile($path, $content)
    {
        $fileSystem = new FileSystem();
        $errorMessage = "An error occurred while creating file.";

        $this->logInfo("Creating file at ".$path);

        try {
            $fileSystem->dumpFile($path, $content);
        } catch (IOException $e) {
            throw new \RuntimeException($errorMessage." File path: ".$e->getPath());
        }
    }


    /**
     * Check if file or folder exists in path
     *
     * @param $path
     * @return bool
     */
    protected function exists($path)
    {
        $fileSystem = new FileSystem();
        return $fileSystem->exists($path);
    }

    /**
     * Creates a directory recursively.
     *
     * @param $path
     */
    protected function mkdir($path)
    {
        $fileSystem = new FileSystem();
        $errorMessage = "An error occurred while creating folder.";

        if ($fileSystem->exists($path) && !is_dir($path)) {
            throw new \RuntimeException($errorMessage." The path '$path' points to a file.");
        }

        if (is_dir($path)) {
            $this->logInfo("Ignored creating directory at ".$path.". Already exists");

            return;
        }

        $this->logInfo("Creating directory at ".$path);

        try {
            $fileSystem->mkdir($path);
        } catch (IOException $e) {
            throw new \RuntimeException($errorMessage." The path: ".$e->getPath());
        }
    }

    /**
     * Download file in stream manner (if possible)
     *
     * @param $srcPath
     * @param $dstPath
     */
    protected function streamDownload($srcPath, $dstPath)
    {
        $this->logInfo("Downloading file at ".$srcPath);
        file_put_contents($dstPath, fopen($srcPath, 'r'));
        $this->logInfo("Download completed. File stored at ".$dstPath);
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