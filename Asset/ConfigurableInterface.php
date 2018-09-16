<?php

namespace App\Erfans\AssetBundle\Asset;

interface ConfigurableInterface {

    /**
     * Set a related configuration to the agent from erfans_asset config
     *
     * @param array $config
     *
     * @return void
     */
    public function setConfig(array $config);
}
