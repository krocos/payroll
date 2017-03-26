<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ManualCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('manual')
            ->addArgument('start_date', InputArgument::REQUIRED, 'Время начала в формате гггг-мм-дд чч:мм:сс')
            ->addArgument('end_date', InputArgument::REQUIRED, 'Время завершения в формате гггг-мм-дд чч:мм:сс')
            ->addOption('note', null, InputOption::VALUE_OPTIONAL, 'Заметка к интервалу')
            ->setDescription('Добавляет мануальный интервал');
    }

    public function setContainer(ContainerInterface $container): ManualCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('manager.item_manager')
            ->addManualInterval($output, $input->getArgument('start_date'), $input->getArgument('end_date'), $input->getOption('note'));
    }
}
