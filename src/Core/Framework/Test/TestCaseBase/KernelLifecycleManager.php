<?php declare(strict_types=1);

namespace Shopware\Core\Framework\Test\TestCaseBase;

use Composer\Autoload\ClassLoader;
use Shopware\Core\Framework\Test\Filesystem\Adapter\MemoryAdapterFactory;
use Shopware\Core\Kernel;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ResettableContainerInterface;

class KernelLifecycleManager
{
    protected static $class;

    /**
     * @var Kernel|null
     */
    protected static $kernel;

    /**
     * Get the currently active kernel
     */
    public static function getKernel(): Kernel
    {
        if (static::$kernel) {
            return static::$kernel;
        }

        return static::bootKernel();
    }

    /**
     * Create a web client with the default kernel and disabled reboots
     */
    public static function createClient(Kernel $kernel, bool $enableReboot = false): Client
    {
        /** @var Client $apiClient */
        $apiClient = $kernel->getContainer()->get('test.client');

        if ($enableReboot) {
            $apiClient->enableReboot();
        } else {
            $apiClient->disableReboot();
        }

        return $apiClient;
    }

    /**
     * Boots the Kernel for this test.
     */
    public static function bootKernel($plugins = []): Kernel
    {
        static::ensureKernelShutdown();

        static::$kernel = static::createKernel();
        foreach ($plugins as $plugin) {
            Kernel::getPlugins()->add($plugin);
        }
        static::$kernel->boot();
        MemoryAdapterFactory::resetInstances();

        return static::$kernel;
    }

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     */
    private static function getKernelClass(): string
    {
        if (!isset($_SERVER['KERNEL_CLASS']) && !isset($_ENV['KERNEL_CLASS'])) {
            throw new \LogicException(sprintf('You must set the KERNEL_CLASS environment variable to the fully-qualified class name of your Kernel in phpunit.xml / phpunit.xml.dist or override the %1$s::createKernel() or %1$s::getKernelClass() method.', static::class));
        }

        if (!class_exists($class = $_ENV['KERNEL_CLASS'] ?? $_SERVER['KERNEL_CLASS'])) {
            throw new \RuntimeException(sprintf('Class "%s" doesn\'t exist or cannot be autoloaded. Check that the KERNEL_CLASS value in phpunit.xml matches the fully-qualified class name of your Kernel or override the %s::createKernel() method.', $class, static::class));
        }

        return $class;
    }

    private static function createKernel(): Kernel
    {
        if (static::$class === null) {
            static::$class = static::getKernelClass();
        }

        if (isset($_ENV['APP_ENV'])) {
            $env = $_ENV['APP_ENV'];
        } elseif (isset($_SERVER['APP_ENV'])) {
            $env = $_SERVER['APP_ENV'];
        } else {
            $env = 'test';
        }

        if (isset($_ENV['APP_DEBUG'])) {
            $debug = (bool) $_ENV['APP_DEBUG'];
        } elseif (isset($_SERVER['APP_DEBUG'])) {
            $debug = (bool) $_SERVER['APP_DEBUG'];
        } else {
            $debug = true;
        }

        return new static::$class($env, $debug, new ClassLoader());
    }

    /**
     * Shuts the kernel down if it was used in the test.
     */
    private static function ensureKernelShutdown(): void
    {
        if (static::$kernel === null) {
            return;
        }

        $container = static::$kernel->getContainer();
        static::$kernel->shutdown();

        if ($container instanceof ResettableContainerInterface) {
            $container->reset();
        }
    }
}
