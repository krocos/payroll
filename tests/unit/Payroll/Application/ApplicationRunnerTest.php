<?php

namespace Payroll\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationRunnerTest extends TestCase
{
    public function testRunnerStartsApplicationProperly()
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();

        $application = $this
            ->getMockBuilder(Application::class)
            ->setMethods(['createContainer', 'run'])
            ->getMock();
        $application->expects($this->once())->method('createContainer')->willReturn($container);
        $application->expects($this->once())->method('run')->with($this->equalTo($container));

        $runner = $this
            ->getMockBuilder(ApplicationRunner::class)
            ->setMethods(['createApplication'])
            ->getMock();
        $runner->expects($this->once())->method('createApplication')->willReturn($application);

        $runner->run();
    }

    public function testApplicationCreatesAsExpected()
    {
        $applicationRunner = new ApplicationRunner();
        $app = $applicationRunner->createApplication();

        $this->assertInstanceOf(Application::class, $app);
    }
}
