<?php

namespace Payroll\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Инициализирует хранилище');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (file_exists(__DIR__.'/../../../var/database.sqlite')) {
            $output->writeln('Инициализация уже была произведена.');
            return;
        }

        $pdo = new \PDO('sqlite:'.__DIR__.'/../../../var/database.sqlite');
        $pdo->exec('CREATE TABLE item (id INTEGER NOT NULL, sheet_id INTEGER DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, note CLOB DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_1F1B251E8B1206A5 FOREIGN KEY (sheet_id) REFERENCES sheet (id) NOT DEFERRABLE INITIALLY IMMEDIATE);');
        $pdo->exec('CREATE INDEX IDX_1F1B251E8B1206A5 ON item (sheet_id);');
        $pdo->exec('CREATE TABLE sheet (id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id));');

        $output->writeln('Можно работать!');
    }
}
