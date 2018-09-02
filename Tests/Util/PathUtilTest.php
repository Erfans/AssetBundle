<?php

namespace App\Erfans\AssetBundle\Tests\Util;

use Erfans\AssetBundle\Util\PathUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PathUtilTest extends KernelTestCase {

    /**
     * @var PathUtil
     */
    private $pathUtil;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        $kernel = self::bootKernel();

        $this->pathUtil = $kernel->getContainer()->get('erfans_asset.util.path');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetBundleFile() {
        $file = $this->pathUtil->getBundleFile("ErfansAssetBundle", "README.md");

        self::assertTrue(is_file($file));
    }

    public function testGetContent() {
        $file = $this->pathUtil->getBundleFile("ErfansAssetBundle", "README.md");
        $content = $this->pathUtil->getContent($file);

        self::assertContains("ErfansAssetBundle", $content);
    }

    public function testGetBundleDirectory() {
        $directory = $this->pathUtil->getBundleDirectory("ErfansAssetBundle");

        self::assertTrue(is_dir($directory));
        self::assertContains("AssetBundle", $directory);
    }

    public function testJoinPaths() {
        $this->assertEquals($this->pathUtil->joinPaths("a", "b"), "a/b");
        $this->assertEquals($this->pathUtil->joinPaths("a", "/b"), "a/b");
        $this->assertEquals($this->pathUtil->joinPaths("a/", "/b"), "a/b");
        $this->assertEquals($this->pathUtil->joinPaths("/", "a/", "/b"), "/a/b");
        $this->assertEquals($this->pathUtil->joinPaths("//", "a/", "/b"), "/a/b");
    }
}
