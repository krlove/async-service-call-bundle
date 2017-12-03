<?php

namespace Krlove\AsyncServiceCallBundle;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class AsyncService
 * @package Krlove\AsyncServiceCallBundle
 */
class AsyncService
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $consolePath;

    /**
     * @var string
     */
    protected $phpPath;

    /**
     * AsyncService constructor.
     * @param Filesystem $filesystem
     * @param string $rootDir
     * @param string $consolePath
     * @param string $phpPath
     */
    public function __construct(Filesystem $filesystem, $rootDir, $consolePath, $phpPath)
    {
        $this->filesystem = $filesystem;
        $this->rootDir = $rootDir;

        $this->setConsolePath($consolePath);
        $this->setPhpPath($phpPath);
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
        $arguments = escapeshellarg(base64_encode(serialize($arguments)));

        return sprintf(
            '%s %s krlove:service:call %s %s --args=%s > /dev/null 2>/dev/null &',
            $this->phpPath,
            $this->consolePath,
            $service,
            $method,
            $arguments
        );
    }

    /**
     * @param string $consolePath
     */
    protected function setConsolePath($consolePath)
    {
        if (!$this->filesystem->isAbsolutePath($consolePath)) {
            $consolePath = $this->rootDir . '/../' . $consolePath;
        }

        if (!$this->filesystem->exists($consolePath)) {
            throw new FileNotFoundException(sprintf('File %s doesn\'t exist', $consolePath));
        }

        $this->consolePath = $consolePath;
    }

    /**
     * @param string $phpPath
     */
    protected function setPhpPath($phpPath)
    {
        if ($phpPath === null) {
            $finder = new PhpExecutableFinder();
            $phpPath = $finder->find();
        }

        if (!$this->filesystem->exists($phpPath)) {
            throw new FileNotFoundException(sprintf('PHP executable %s doesn\'t exist', $phpPath));
        }

        $this->phpPath = $phpPath;
    }
}
