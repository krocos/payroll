<?php

namespace Payroll\Application;

class ApplicationRunner
{
    public function run()
    {
        $application = $this->createApplication();
        $container = $application->createContainer();
        $application->run($container);
    }

    /**
     * @return Application
     */
    private function createApplication(): Application
    {
        return new Application();
    }
}
