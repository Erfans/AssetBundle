<?php

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
}
