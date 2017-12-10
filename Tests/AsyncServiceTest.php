<?php

namespace Krlove\AsyncServiceCallBundle\Tests;

use Krlove\AsyncServiceCallBundle\AsyncService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AsyncServiceTest
 * @package Krlove\AsyncServiceCallBundle\Tests
 */
class AsyncServiceTest extends TestCase
{
    /**
     * @param string $phpPath
     * @param string $consolePath
     * @param string $service
     * @param string $method
     * @param array $arguments
     * @param string $commandString
     *
     * @dataProvider argumentsProvider
     */
    public function testCall($phpPath, $consolePath, $service, $method, $arguments, $commandString)
    {
        /** @var AsyncService|MockObject $asyncService */
        $asyncService = $this->getMockBuilder(AsyncService::class)
            ->setMethods(['runProcess'])
            ->setConstructorArgs([$consolePath, $phpPath])
            ->getMock();

        $asyncService->expects($this->once())
            ->method('runProcess')
            ->with($commandString)
            ->willReturn(10);

        $pid = $asyncService->call($service, $method, $arguments);
        $this->assertEquals(10, $pid);
    }

    /**
     * @return array
     */
    public function argumentsProvider()
    {
        return [
            [
                'php_path' => '/php/path',
                'console_path' => '/console/path',
                'service' => 'service_id',
                'method' => 'method',
                'arguments' => [1, 'str', ['arr']],
                'command_string' => '/php/path /console/path krlove:service:call service_id method --args=\'YTozOntpOjA7aToxO2k6MTtzOjM6InN0ciI7aToyO2E6MTp7aTowO3M6MzoiYXJyIjt9fQ==\' > /dev/null 2>/dev/null &',
            ],
        ];
    }
}
