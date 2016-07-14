<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/2/2016
 * Time: 21:11
 */

namespace Erfans\AssetBundle\Asset;


class Config
{

    /** @var array $config */
    private $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Is all bundles are added
     *
     * @return boolean
     */
    public function isAllBundles()
    {
        return $this->config["all_bundles"];
    }

    /**
     * get names of registered bundles
     *
     * @return array
     */
    public function getBundles()
    {
        return $this->config["bundles"];
    }

    /**
     * get names of registered agents
     *
     * @return array
     */
    public function getAgents()
    {
        return array_merge($this->getDownloadAgent(), $this->getOptimizeAgent(), $this->getReferenceAgent());
    }

    /**
     * @return array
     */
    public function getDownloadAgent()
    {
        return $this->config["download"];
    }

    /**
     * @return array
     */
    public function getReferenceAgent()
    {
        return $this->config["reference"];
    }

    /**
     * @return array
     */
    public function getOptimizeAgent()
    {
        return $this->config["optimize"];
    }
}