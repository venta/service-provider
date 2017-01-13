<?php declare(strict_types = 1);

namespace Venta\ServiceProvider;

use Venta\Contracts\Config\MutableConfig;
use Venta\Contracts\Console\CommandCollection;
use Venta\Contracts\Container\MutableContainer;
use Venta\Contracts\Kernel\Kernel;
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
     * @var MutableConfig
     */
    private $config;

    /**
     * Container instance.
     *
     * @var MutableContainer
     */
    private $container;

    /**
     * AbstractServiceProvider constructor.
     *
     * @param MutableContainer $mutableContainer
     * @param MutableConfig $mutableConfig
     */
    public function __construct(MutableContainer $mutableContainer, MutableConfig $mutableConfig)
    {
        $this->container = $mutableContainer;
        $this->config = $mutableConfig;
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
     * @return MutableConfig
     */
    protected function config(): MutableConfig
    {
        return $this->config;
    }

    /**
     * @return MutableContainer
     */
    protected function container(): MutableContainer
    {
        return $this->container;
    }

    /**
     * @return Kernel
     */
    protected function kernel(): Kernel
    {
        return $this->container()->get(Kernel::class);
    }

    /**
     * Merges config params from service provider with the global configuration.
     *
     * @param string[] ...$configFiles
     */
    protected function loadConfigFromFiles(string ...$configFiles)
    {
        foreach ($configFiles as $configFile) {
            $this->config->merge(require $configFile);
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