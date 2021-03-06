<?php

namespace App\Erfans\AssetBundle\Tests\Util;

use Erfans\AssetBundle\Util\FileSystem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileSystemTest extends KernelTestCase {

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        self::bootKernel();

        $this->fileSystem = self::$container->get('erfans_asset.util.file_system');
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetBundleDirectory() {
        $directory = $this->fileSystem->getBundleDirectory("ErfansAssetBundle");

        self::assertTrue(is_dir($directory));
        self::assertContains("AssetBundle", $directory);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetBundleFile() {
        $file = $this->fileSystem->getBundleFile("ErfansAssetBundle", "README.md");

        self::assertTrue(is_file($file));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetProjectRelativePath() {
        $file = $this->fileSystem->getProjectRelativePath("public/index.php");

        self::assertTrue(is_file($file));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetContent() {
        $file = $this->fileSystem->getBundleFile("ErfansAssetBundle", "README.md");
        $content = $this->fileSystem->getContent($file);

        self::assertContains("ErfansAssetBundle", $content);
    }

    public function testJoinPaths() {
        $this->assertEquals($this->fileSystem->joinPaths("a", "b"), "a/b");
        $this->assertEquals($this->fileSystem->joinPaths("a", "/b"), "a/b");
        $this->assertEquals($this->fileSystem->joinPaths("a/", "/b"), "a/b");
        $this->assertEquals($this->fileSystem->joinPaths("/", "a/", "/b"), "/a/b");
        $this->assertEquals($this->fileSystem->joinPaths("//", "a/", "/b"), "/a/b");
    }
}
