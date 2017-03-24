<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once __DIR__.'/../bootstrap.php';

/** @var EntityManager $entityManager */
$entityManager = (new \Payroll\Application\Application())->createContainer()->get('doctrine.orm.entity_manager');

return ConsoleRunner::createHelperSet($entityManager);
