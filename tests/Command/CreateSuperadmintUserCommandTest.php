<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateSuperadmintUserCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();

        $application = new Application($kernel);
        $command = $application->find('basicrum:superadmin:create');

        $commandTester = new CommandTester($command);

        $commandTester->setInputs(['FirstName', 'LastName', 'email@email.lc', '123456', '123456']);

        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/\[OK\] Super admin user with email email@email\.lc has been created!/', $output);
    }
}
