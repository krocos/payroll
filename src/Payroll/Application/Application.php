<?php

namespace Payroll\Application;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as CommandlineApplication;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application
{
    public function createContainer(): ContainerInterface
    {
        $container = new ContainerBuilder();

        $container->set('doctrine.orm.entity_manager', $this->createEntityManager());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../../config'));
        $loader->load('services.yml');

        return $container;
    }

    public function run(ContainerInterface $container)
    {
        $application = $this->createCommandlineApplication();

        $application->addCommands(
            [
                $container->get('command.sheet')->setContainer($container),
                $container->get('command.start')->setContainer($container),
                $container->get('command.stop')->setContainer($container),
                $container->get('command.now')->setContainer($container),
                $container->get('command.display')->setContainer($container),
                $container->get('command.manual')->setContainer($container),
                $container->get('command.delete')->setContainer($container),
            ]
        );

        $application->run();
    }

    private function createEntityManager(): EntityManager
    {
        return EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'path' => __DIR__.'/../../../var/database.sqlite',
            ],
            Setup::createAnnotationMetadataConfiguration(
                [__DIR__.'/../Entity'],
                true,
                null,
                null,
                false
            )
        );
    }

    /**
     * @return CommandlineApplication
     */
    private function createCommandlineApplication(): CommandlineApplication
    {
        return new CommandlineApplication();
    }
}
