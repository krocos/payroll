<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DisplayCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('display')
            ->addOption('hourlyrate', 'r', InputOption::VALUE_OPTIONAL, 'Стоимость часа работы (float)')
            ->addOption('id', 'i', InputOption::VALUE_NONE, 'Выводить id интервалов')
            ->setDescription('Выводит данные по активному листу в разных форматах');
    }

    public function setContainer(ContainerInterface $container): DisplayCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $hourlyrate = $input->getOption('hourlyrate');
        // Если у нас есть rate, то каст к float
        if ($hourlyrate) {
            $hourlyrate = (float) $hourlyrate;
        }

        $this->container->get('manager.sheet_manager')->display($output, $hourlyrate, $input->getOptions()['id']);
    }
}
