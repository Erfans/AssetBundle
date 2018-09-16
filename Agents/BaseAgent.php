<?php

namespace Erfans\AssetBundle\Agents;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseAgent
 *
 * @package Erfans\AssetBundle\Agents
 * Holds common tools of agents
 */
abstract class BaseAgent implements InstallerInterface {

    /** @var LoggerInterface $logger */
    protected $logger;

    /** @var InputInterface $consoleInput */
    protected $consoleInput;

    /** @var OutputInterface $consoleOutput */
    protected $consoleOutput;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function setConsoleInterface(InputInterface $input, OutputInterface $output) {
        $this->consoleInput = $input;
        $this->consoleOutput = $output;
    }

    /**
     * Convert keys in an array to values of other array
     *
     * @param array $config
     * @param array $map
     *
     * @return array
     */
    protected function normalizeConfig(array $config = [], array $map = []) {
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
}
