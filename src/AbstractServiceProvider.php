<?php declare(strict_types = 1);

namespace Venta\ServiceProvider;

use Venta\Contracts\Config\Config;
use Venta\Contracts\Config\ConfigFactory;
use Venta\Contracts\Console\CommandCollector;
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
     * Container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Application config.
     *
     * @var Config
     */
    private $appConfig;

    /**
     * AbstractServiceProvider constructor.
     *
     * @param Container $container
     * @param Config $appConfig
     */
    public function __construct(Container $container, Config $appConfig)
    {
        $this->container = $container;
        $this->appConfig = $appConfig;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    final public static function getName(): string
    {
        return static::$name ?: static::class;
    }

    /**
     * @inheritdoc
     */
    abstract public function boot();

    /**
     * Registers commands exposed by service provider.
     *
     * @param string[] ...$commandClasses
     */
    protected function provideCommands(string ...$commandClasses)
    {
        /** @var CommandCollector $commandCollector */
        $commandCollector = $this->container->get(CommandCollector::class);
        foreach ($commandClasses as $commandClass) {
            $commandCollector->addCommand($commandClass);
        }
    }

    /**
     * Merges config params from service provider with the global configuration.
     *
     * @param string[] ...$configFiles
     */
    protected function loadConfigFromFiles(string ...$configFiles)
    {
        /** @var Config $config */
        $config = $this->container->get(Config::class);

        /** @var ConfigFactory $configFactory */
        $configFactory = $this->container->get(ConfigFactory::class);
        foreach ($configFiles as $configFile) {
            $config = $config->merge($configFactory->createFromFile($configFile));
        }

        $config = $config->merge($this->appConfig);

        $this->container->set(Config::class, $config);

    }
}