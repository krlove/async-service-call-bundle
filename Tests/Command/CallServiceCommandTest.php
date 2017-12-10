<?php

namespace Krlove\AsyncServiceCallBundle\Tests\Command;

use Krlove\AsyncServiceCallBundle\Command\CallServiceCommand;
use Krlove\AsyncServiceCallBundle\Tests\Command\Mock\ServiceMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CallServiceCommandTest
 * @package Krlove\AsyncServiceCallBundle\Tests\Command
 */
class CallServiceCommandTest extends TestCase
{
    /**
     * @var CallServiceCommand
     */
    protected $command;

    /**
     * @var ContainerInterface|MockObject
     */
    protected $container;

    /**
     * @var InputInterface|MockObject
     */
    protected $input;

    /**
     * @var OutputInterface|MockObject
     */
    protected $output;

    /**
     * @var Logger|MockObject
     */
    protected $logger;

    /**
     * @var ServiceMock|MockObject
     */
    protected $serviceMock;

    protected function setUp()
    {
        $this->command = new CallServiceCommand();
        $this->container = $this->createMock(ContainerInterface::class);
        $this->command->setContainer($this->container);
        $this->input = $this->createMock(InputInterface::class);
        $this->output = $this->createMock(OutputInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->serviceMock = $this->createMock(ServiceMock::class);
    }

    public function testExecute()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('logger')
            ->willReturn($this->logger);

        $this->input->expects($this->at(0))
            ->method('getArgument')
            ->with('service')
            ->willReturn('service_id');

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('service_id')
            ->willReturn($this->serviceMock);

        $this->input->expects($this->at(1))
            ->method('getArgument')
            ->with('method')
            ->willReturn('someMethod');

        $this->input->expects($this->at(2))
            ->method('getOption')
            ->with('args')
            ->willReturn('');

        $this->serviceMock->expects($this->once())
            ->method('someMethod');

        $this->execute();
    }

    public function testServiceNotExist()
    {
        $exception = new \Exception('Service not found');

        $this->container->expects($this->at(0))
            ->method('get')
            ->with('logger')
            ->willReturn($this->logger);

        $this->input->expects($this->at(0))
            ->method('getArgument')
            ->with('service')
            ->willReturn('service_id');

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('service_id')
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('Service not found');

        $this->execute();
    }

    public function testMethodNotExist()
    {
        $this->container->expects($this->at(0))
            ->method('get')
            ->with('logger')
            ->willReturn($this->logger);

        $this->input->expects($this->at(0))
            ->method('getArgument')
            ->with('service')
            ->willReturn('service_id');

        $this->container->expects($this->at(1))
            ->method('get')
            ->with('service_id')
            ->willReturn($this->serviceMock);

        $this->input->expects($this->at(1))
            ->method('getArgument')
            ->with('method')
            ->willReturn('noSuchMethod');

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->matchesRegularExpression("/Method noSuchMethod doesn\'t exist on class .*ServiceMock.*/"));

        $this->execute();
    }

    protected function execute()
    {
        $reflectionMethod = new \ReflectionMethod($this->command, 'execute');
        $reflectionMethod->setAccessible(true);
        $reflectionMethod->invoke($this->command, $this->input, $this->output);
    }
}
