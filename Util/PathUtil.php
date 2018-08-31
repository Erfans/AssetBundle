<?php

namespace Erfans\AssetBundle\Util;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PathUtil {

    /** @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params */
    private $params;

    /**
     * PathUtil constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $params
     */
    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    /**
     * @param string $bundle
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getBundleDirectory(string $bundle): string {
        $bundles = $this->params->get('kernel.bundles');

        if (!array_key_exists($bundle, $bundles)) {
            throw new \InvalidArgumentException("Bundle '$bundle' not found in the registered bundles.");
        }

        $bundlePath = $bundles[$bundle];
        $reflection = new \ReflectionClass($bundlePath);

        return dirname($reflection->getFilename());
    }

    /**
     * @param string $bundleName
     * @param string $relativeFilePath
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getBundleFile(string $bundleName, string $relativeFilePath) {
        $bundleDirectory = $this->getBundleDirectory($bundleName);

        return realpath($this->joinPaths($bundleDirectory, $relativeFilePath));
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function getContent(string $file): string {
        if (!is_file($file)) {
            throw new \InvalidArgumentException("Path '$file' is not a file.");
        }

        return file_get_contents($file);
    }

    /**
     * @return string
     */
    function joinPaths(): string {
        $paths = [];

        foreach (func_get_args() as $arg) {
            if ($arg !== '') {
                $paths[] = $arg;
            }
        }

        return preg_replace('#/+#', '/', join('/', $paths));
    }
}
