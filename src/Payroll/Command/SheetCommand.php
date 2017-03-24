<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SheetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('sheet')
            ->addArgument('name', InputArgument::REQUIRED, 'Название листа для для работы или для переключения фокуса')
            ->setDescription('Выбрать или переключить активный лист');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
