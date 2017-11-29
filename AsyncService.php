<?php

namespace Krlove\AsyncServiceCallBundle;

use Symfony\Component\Process\Process;

/**
 * Class AsyncService
 * @package Krlove\AsyncServiceCallBundle
 */
class AsyncService
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * AsyncService constructor.
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $arguments
     * @return int|null
     */
    public function call($service, $method, $arguments = [])
    {
        $commandline = $this->createCommandString($service, $method, $arguments);

        $process = new Process($commandline);
        $process->setWorkingDirectory($this->rootDir . '/../');
        $process->start();

        return $process->getPid();
    }

    /**
     * @param string $service
     * @param string $method
     * @param array $arguments
     * @return string
     */
    protected function createCommandString($service, $method, $arguments)
    {
        $arguments = escapeshellarg(serialize($arguments));

        return sprintf(
            'bin/console krlove:service:call %s %s --args=%s > /dev/null 2>/dev/null &', // todo configure entry point
            $service,
            $method,
            $arguments
        );
    }
}
