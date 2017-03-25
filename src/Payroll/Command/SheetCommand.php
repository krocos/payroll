<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SheetCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('sheet')
            ->addArgument('name', InputArgument::OPTIONAL, 'Название листа для для работы или для переключения фокуса')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'Для удаления листа')
            ->addOption('list', 'l', InputOption::VALUE_NONE, 'Показать список листов')
            ->setDescription('Выбрать или переключить активный лист');
    }

    public function setContainer(ContainerInterface $container): SheetCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Если надо показать список листов
        if ($input->getOptions()['list']) {
            $this->container->get('manager.sheet_manager')->listSheets($output);
        }
        // Иначе делаем манипуляции с листами
        else {
            $sheetName = $input->getArguments()['name'] ?? false;
            if ($sheetName) {
                $this->container->get('manager.sheet_manager')
                    ->handleSheet($output, $input->getArgument('name'), $input->getOptions()['delete']);
            } else {
                $output->writeln("Необходимо указать имя листа.");
            }
        }

    }
}
