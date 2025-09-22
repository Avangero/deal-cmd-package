<?php

declare(strict_types=1);

namespace Avangero\DealCmdPackage\Infrastructure\Laravel;

use Avangero\DealCmdPackage\Application\Services\CommandProcessor;
use Avangero\DealCmdPackage\Domain\Commands\AcceptedCommand;
use Avangero\DealCmdPackage\Domain\Commands\CloseReasonCommand;
use Avangero\DealCmdPackage\Domain\Commands\ContactCommand;
use Avangero\DealCmdPackage\Domain\Commands\ReasonCommand;
use Avangero\DealCmdPackage\Domain\Configuration\CommandConfigurationInterface;
use Avangero\DealCmdPackage\Domain\Ports\LoggerInterface;
use Avangero\DealCmdPackage\Domain\Services\CommandParser;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandMapperInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\CommandParserInterface;
use Avangero\DealCmdPackage\Domain\Services\Interfaces\MessageProviderInterface;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealIdFactory;
use Avangero\DealCmdPackage\Domain\ValueObjects\PropertyIdFactory;
use Avangero\DealCmdPackage\Infrastructure\Laravel\Adapters\LaravelLoggerAdapter;
use Avangero\DealCmdPackage\Infrastructure\Laravel\Configuration\ArrayCommandConfiguration;
use Avangero\DealCmdPackage\Infrastructure\Laravel\Services\ArrayCommandMapper;
use Avangero\DealCmdPackage\Infrastructure\Laravel\Services\ArrayMessageProvider;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

final class DealCmdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CommandParserInterface::class, function ($app) {
            return new CommandParser($app->make(MessageProviderInterface::class));
        });

        $this->app->bind(LoggerInterface::class, function ($app) {
            return new LaravelLoggerAdapter($app->make(PsrLoggerInterface::class));
        });

        $this->app->bind(PropertyIdFactory::class, function ($app) {
            return new PropertyIdFactory($app->make(MessageProviderInterface::class));
        });

        $this->app->bind(DealIdFactory::class, function ($app) {
            return new DealIdFactory($app->make(MessageProviderInterface::class));
        });

        $this->app->bind(CommandConfigurationInterface::class, function ($app) {
            $config = $app->make('config')->get('deal-cmd', []);

            return new ArrayCommandConfiguration(
                $config,
                $app->make(PropertyIdFactory::class)
            );
        });

        $this->app->bind(MessageProviderInterface::class, function ($app) {
            $messages = $app->make('config')->get('deal-cmd-messages', []);

            return new ArrayMessageProvider($messages);
        });

        $this->app->bind(CommandMapperInterface::class, function ($app) {
            $mapping = $app->make('config')->get('deal-cmd-mapping', []);

            return new ArrayCommandMapper($mapping);
        });

        $this->app->bind(AcceptedCommand::class);
        $this->app->bind(ContactCommand::class);
        $this->app->bind(CloseReasonCommand::class);
        $this->app->bind(ReasonCommand::class);

        $this->app->bind(CommandProcessor::class, function ($app) {
            $commands = [
                $app->make(AcceptedCommand::class),
                $app->make(ContactCommand::class),
                $app->make(CloseReasonCommand::class),
                $app->make(ReasonCommand::class),
            ];

            return new CommandProcessor(
                $app->make(CommandParserInterface::class),
                $app->make(LoggerInterface::class),
                $app->make(MessageProviderInterface::class),
                $app->make(CommandMapperInterface::class),
                $commands
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/deal-cmd.php' => config_path('deal-cmd.php'),
        ], 'deal-cmd-config');

        $this->publishes([
            __DIR__ . '/../../../config/messages.php' => config_path('deal-cmd-messages.php'),
        ], 'deal-cmd-messages');

        $this->publishes([
            __DIR__ . '/../../../config/command-mapping.php' => config_path('deal-cmd-mapping.php'),
        ], 'deal-cmd-mapping');
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CommandParserInterface::class,
            LoggerInterface::class,
            MessageProviderInterface::class,
            CommandMapperInterface::class,
            CommandConfigurationInterface::class,
            CommandProcessor::class,
            AcceptedCommand::class,
            ContactCommand::class,
            CloseReasonCommand::class,
            ReasonCommand::class,
            PropertyIdFactory::class,
            DealIdFactory::class,
        ];
    }
}
