<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EditCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('edit')
            ->addArgument('item_id', InputArgument::REQUIRED, 'id интервала для редактирования')
            ->addOption('start', 's', InputOption::VALUE_OPTIONAL, 'Новая дата начала в формате гггг-мм-дд чч:мм:сс')
            ->addOption('end', 'e', InputOption::VALUE_OPTIONAL, 'Новая дата завершения в формате гггг-мм-дд чч:мм:сс')
            ->addOption('note', null, InputOption::VALUE_OPTIONAL, 'Новая заметка к интервалу')
            ->addOption('append', 'a', InputOption::VALUE_OPTIONAL, 'Добавляет ", " и то, что указано к заметке')
            ->setDescription('Редактирование интервала по id');
    }

    public function setContainer(ContainerInterface $container): EditCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start = $input->getOption('start');
        $end = $input->getOption('end');
        $note = $input->getOption('note');
        $append = $input->getOption('append');

        if (is_null($start) && is_null($end) && is_null($note) && is_null($append)) {
            $output->writeln('Необходимо задать хотя бы одну опцию из <fg=cyan>start</>, <fg=cyan>end</> или <fg=cyan>note</> или несколько.');
            return;
        }

        if (!is_null($note) && !is_null($append)) {
            $output->writeln("Опции note и append не могут использоваться одновременно.");
        }

        $this->container->get('manager.item_manager')->edit($output, $input->getArgument('item_id'), $start, $end, $note, $append);
    }
}
