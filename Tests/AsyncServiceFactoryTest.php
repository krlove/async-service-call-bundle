<?php

namespace Krlove\AsyncServiceCallBundle\Tests;

use Krlove\AsyncServiceCallBundle\AsyncServiceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AsyncServiceFactoryTest
 * @package Tests\Krlove\AsyncServiceCallBundle
 */
class AsyncServiceFactoryTest extends TestCase
{
    /**
     * @dataProvider correctArgumentsProvider
     */
    public function testCreateAsyncServiceSuccess(
        $rootDir,
        $consolePath,
        $isConsolePathAbsolute,
        $absoluteConsolePath,
        $phpPath
    ) {
        /** @var Filesystem|MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($this->once())
            ->method('isAbsolutePath')
            ->with($consolePath)
            ->willReturn($isConsolePathAbsolute);

        $filesystem->expects($this->at(1))
            ->method('exists')
            ->with($absoluteConsolePath)
            ->willReturn(true);

        $filesystem->expects($this->at(2))
            ->method('exists')
            ->with($phpPath)
            ->willReturn(true);

        $asyncServiceFactory = new AsyncServiceFactory($filesystem, $rootDir, $consolePath, $phpPath);
        $asyncService = $asyncServiceFactory->createAsyncService();

        $reflConsolePathProp = new \ReflectionProperty($asyncService, 'consolePath');
        $reflConsolePathProp->setAccessible(true);
        $reflConsolePathPropValue = $reflConsolePathProp->getValue($asyncService);

        $reflPhpPathProp = new \ReflectionProperty($asyncService, 'phpPath');
        $reflPhpPathProp->setAccessible(true);
        $reflPhpPathPropValue = $reflPhpPathProp->getValue($asyncService);

        $this->assertEquals($absoluteConsolePath, $reflConsolePathPropValue);
        $this->assertEquals($phpPath, $reflPhpPathPropValue);
    }

    public function correctArgumentsProvider()
    {
        return [
            [
                'root_dir' => '/root/dir',
                'console_path' => '/console/path',
                'is_console_path_absolute' => true,
                'absolute_console_path' => '/console/path',
                'php_path' => '/php/path',
            ],
            [
                'root_dir' => '/root/dir',
                'console_path' => 'console/path',
                'is_console_path_absolute' => false,
                'absolute_console_path' => '/root/dir/../console/path',
                'php_path' => '/php/path',
            ],
        ];
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @expectedExceptionMessage File /console/path doesn't exist
     */
    public function testCreateAsyncServiceConsoleNotExist()
    {
        /** @var Filesystem|MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($this->once())
            ->method('isAbsolutePath')
            ->with('/console/path')
            ->willReturn(true);

        $filesystem->expects($this->once())
            ->method('exists')
            ->with('/console/path')
            ->willReturn(false);

        $asyncServiceFactory = new AsyncServiceFactory($filesystem, '/root/dir', '/console/path', '/php/path');
        $asyncServiceFactory->createAsyncService();
    }

    /**
     * @expectedException \Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @expectedExceptionMessage PHP executable /php/path doesn't exist
     */
    public function testCreateAsyncServicePhpNotExist()
    {
        /** @var Filesystem|MockObject $filesystem */
        $filesystem = $this->createMock(Filesystem::class);

        $filesystem->expects($this->once())
            ->method('isAbsolutePath')
            ->with('/console/path')
            ->willReturn(true);

        $filesystem->expects($this->at(1))
            ->method('exists')
            ->with('/console/path')
            ->willReturn(true);

        $filesystem->expects($this->at(2))
            ->method('exists')
            ->with('/php/path')
            ->willReturn(false);

        $asyncServiceFactory = new AsyncServiceFactory($filesystem, '/root/dir', '/console/path', '/php/path');
        $asyncServiceFactory->createAsyncService();
    }
}
