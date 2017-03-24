<?php

namespace Payroll\Application;

use Doctrine\ORM\EntityManager;
use Payroll\Command\SheetCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application as CommandlineApplication;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApplicationTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = new Application();
    }

    public function testContainerCreatesWithoutExceptions()
    {
        $container = $this->application->createContainer();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    public function testEntityManagerCreatesWithoutExceptions()
    {
        $entityManager = $this->application->createEntityManager();

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    public function testCommandlineApplicationCreatesWithoutExceptions()
    {
        $application = $this->application->createCommandlineApplication();

        $this->assertInstanceOf(CommandlineApplication::class, $application);
    }

    public function testRunMethodStartsCommandlineAppProperly()
    {
        $sheetCommand = $this->getMockBuilder(SheetCommand::class)->disableOriginalConstructor()->getMock();

        $commandlineApplication = $this->getMockBuilder(CommandlineApplication::class)
            ->setMethods(['addCommands', 'run'])->getMock();
        $commandlineApplication->expects($this->once())->method('addCommands')
            ->with($this->callback(function ($array) {
                $this->assertInstanceOf(SheetCommand::class, $array[0]);

                return true;
            }));
        $commandlineApplication->expects($this->once())->method('run');

        $container = $this->getMockBuilder(Container::class)->setMethods(['get'])->getMock();
        $container->expects($this->exactly(1))->method('get')->withConsecutive(
            [$this->equalTo('command.sheet')]
        )->willReturnOnConsecutiveCalls($sheetCommand);

        $application = $this->getMockBuilder(Application::class)
            ->setMethods(['createCommandlineApplication'])->getMock();
        $application->expects($this->once())->method('createCommandlineApplication')->willReturn($commandlineApplication);

        $application->run($container);
    }
}
