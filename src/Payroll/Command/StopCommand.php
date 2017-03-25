<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StopCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('stop')
            ->setDescription('Останавливает текущий интервал');
    }

    public function setContainer(ContainerInterface $container): StopCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('manager.item_manager')->stopItem($output);
    }
}
