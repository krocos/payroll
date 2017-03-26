<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('delete')
            ->addArgument('item_id', InputArgument::REQUIRED, 'id интервала для удаления')
            ->setDescription('Удаляет интервал по id');
    }

    public function setContainer(ContainerInterface $container): DeleteCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('manager.item_manager')->delete($output, $input->getArgument('item_id'));
    }
}
