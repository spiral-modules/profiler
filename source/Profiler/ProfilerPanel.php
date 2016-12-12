<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
namespace Spiral\Profiler;

use Monolog\Handler\HandlerInterface;
use Spiral\Core\Bootloaders\Bootloader;
use Spiral\Core\FactoryInterface;
use Spiral\Debug\Benchmarker;
use Spiral\Debug\BenchmarkerInterface;
use Spiral\Debug\LogManager;
use Spiral\Http\HttpDispatcher;

/**
 * Only contain publishable resources and configs.
 */
class ProfilerPanel extends Bootloader
{
    /**
     * Bootable!
     */
    const BOOT = true;

    const BINDINGS   = [
        BenchmarkerInterface::class => Benchmarker::class
    ];

    const SINGLETONS = [
        MemoryHandler::class => [self::class, 'memoryHandler']
    ];

    /**
     * @param LogManager       $logs
     * @param MemoryHandler    $handler
     * @param HttpDispatcher   $http
     * @param FactoryInterface $factory
     */
    public function boot(
        LogManager $logs,
        MemoryHandler $handler,
        HttpDispatcher $http,
        FactoryInterface $factory
    ) {
        //Enabling memory logging for whole application
        $logs->shareHandler($handler);

        $http->riseMiddleware(
            $factory->make(ProfilerWrapper::class, ['started' => microtime(true)])
        );
    }

    /**
     * @return HandlerInterface
     */
    public function memoryHandler(): HandlerInterface
    {
        return new MemoryHandler();
    }
}