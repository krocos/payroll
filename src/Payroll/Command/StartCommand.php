<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StartCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    protected function configure()
    {
        $this
            ->setName('start')
            ->addOption('note', null, InputOption::VALUE_OPTIONAL, 'Заметка к интервалу')
            ->setDescription('Запускает новый интервал в выбраном листе');
    }

    public function setContainer(ContainerInterface $container): StartCommand
    {
        $this->container = $container;

        return $this;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container->get('manager.item_manager')->startItem($output, $input->getOption('note'));
    }
}
