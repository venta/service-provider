<?php declare(strict_types = 1);

namespace Venta\ServiceProvider;

use Venta\Contracts\Config\ConfigBuilder;
use Venta\Contracts\Console\CommandCollection;
use Venta\Contracts\Container\Container;
use Venta\Contracts\ServiceProvider\ServiceProvider;

/**
 * Class AbstractServiceProvider.
 *
 * @package Venta\ServiceProvider
 */
abstract class AbstractServiceProvider implements ServiceProvider
{
    /**
     * Service provider name.
     *
     * @var string
     */
    protected static $name;

    /**
     * Application config.
     *
     * @var ConfigBuilder
     */
    private $configBuilder;

    /**
     * Container instance.
     *
     * @var Container
     */
    private $container;

    /**
     * AbstractServiceProvider constructor.
     *
     * @param Container $container
     * @param ConfigBuilder $configBuilder
     */
    public function __construct(Container $container, ConfigBuilder $configBuilder)
    {
        $this->container = $container;
        $this->configBuilder = $configBuilder;
    }

    /**
     * @inheritdoc
     */
    public static function dependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    final public static function name(): string
    {
        return static::$name ?: static::class;
    }

    /**
     * @inheritdoc
     */
    abstract public function boot();

    /**
     * @return Container
     */
    protected function container(): Container
    {
        return $this->container;
    }

    /**
     * Merges config params from service provider with the global configuration.
     *
     * @param string[] ...$configFiles
     */
    protected function loadConfigFromFiles(string ...$configFiles)
    {
        foreach ($configFiles as $configFile) {
            $this->configBuilder->mergeFile($configFile);
        }
    }

    /**
     * Registers commands exposed by service provider.
     *
     * @param string[] ...$commandClasses
     */
    protected function provideCommands(string ...$commandClasses)
    {
        /** @var CommandCollection $commandCollector */
        $commandCollector = $this->container->get(CommandCollection::class);
        foreach ($commandClasses as $commandClass) {
            $commandCollector->add($commandClass);
        }
    }
}