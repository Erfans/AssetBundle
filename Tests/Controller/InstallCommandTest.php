<?php
/**
 * Created by PhpStorm.
 * User: Erfan
 * Date: 7/10/2016
 * Time: 22:55
 */

namespace Erfans\AssetBundle\Tests\Controller;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Erfans\AssetBundle\Command\InstallCommand;

class InstallCommandTest extends KernelTestCase
{

    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new InstallCommand());

        $command = $application->find('erfans:asset:install');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        //$this->assertRegExp('/.../', $commandTester->getDisplay());
    }

}