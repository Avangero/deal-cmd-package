# Deal Command Package

PHP-пакет для обработки текстовых команд пользователя в контексте сделки (CRM). Независим от фреймворка, с готовой интеграцией в Laravel.

## Возможности

- Парсинг текстовых команд вида `/команда аргументы`
- Паттерн Chain of Responsibility для обработки
- Изолированная доменная логика (чистый PHP)
- Логирование результатов выполнения
- Простая расширяемость новыми командами

Реализованные команды:
- `/принято 500 офис` → устанавливает свойства сделки (сумма и тип)
- `/контакт` → отправляет служебное сообщение с контактом клиента
- `/причина_закрытия удалена транзакция` → устанавливает причину закрытия
- `/причина` → отправляет служебное сообщение с причиной закрытия

## Установка

```bash
composer require avangero/deal-cmd-package
```

Минимальные требования: PHP 8.1+

## Быстрый старт (фреймворк-независимо)

```php
use Avangero\DealCmdPackage\Application\Services\CommandProcessor;
use Avangero\DealCmdPackage\Domain\Services\CommandParser;
use Avangero\DealCmdPackage\Domain\ValueObjects\DealId;

// Реализуйте интерфейсы доменного слоя под вашу систему:
// - DealRepositoryInterface
// - MessageSenderInterface
// - LoggerInterface (можно адаптировать PSR-3)
// - CommandConfigurationInterface
// - CommandMapperInterface
// - MessageProviderInterface

// Затем создайте CommandProcessor, передав зависимости и список команд
$processor = new CommandProcessor(
    parser: new CommandParser($messageProvider),
    logger: $logger,
    messageProvider: $messageProvider,
    commandMapper: $commandMapper,
    commands: [
        $acceptedCommand,
        $contactCommand,
        $closeReasonCommand,
        $reasonCommand,
    ]
);

$result = $processor->process('/принято 500 офис', new DealId(123));
```

## Интеграция с Laravel

Пакет включает провайдер `DealCmdServiceProvider` и автоматически регистрируется через `extra.laravel.providers`.

1) Опубликуйте конфиги:

```bash
php artisan vendor:publish --provider="Avangero\\DealCmdPackage\\Infrastructure\\Laravel\\DealCmdServiceProvider"
```

Будут созданы файлы:
- `config/deal-cmd.php` — конфигурация команд
- `config/deal-cmd-messages.php` — сообщения
- `config/deal-cmd-mapping.php` — маппинг пользовательских команд

2) Зарегистрируйте адаптеры под вашу CRM в `AppServiceProvider`:

```php
use Avangero\\DealCmdPackage\\Domain\\Ports\\DealRepositoryInterface;
use Avangero\\DealCmdPackage\\Domain\\Ports\\MessageSenderInterface;
use App\\Services\\CrmDealRepository;
use App\\Services\\CrmMessageSender;

public function register(): void
{
    $this->app->bind(DealRepositoryInterface::class, CrmDealRepository::class);
    $this->app->bind(MessageSenderInterface::class, CrmMessageSender::class);
}
```

3) Использование (например, в контроллере):

```php
use Avangero\\DealCmdPackage\\Application\\Services\\CommandProcessor;
use Avangero\\DealCmdPackage\\Domain\\ValueObjects\\DealId;

public function processCommand(Request $request, CommandProcessor $processor)
{
    $result = $processor->process($request->string('command'), new DealId((int) $request->input('deal_id')));

    return response()->json([
        'success' => $result->isSuccess(),
        'message' => $result->isSuccess() ? $result->getMessage() : $result->getErrorMessage(),
    ]);
}
```

## Конфигурация

`config/deal-cmd.php` (ID свойств):

```php
return [
    'accepted' => [
        'amount_property' => 14,
        'type_property' => 15,
    ],
    'close_reason' => [
        'close_reason_property' => 222,
    ],
    'reason' => [
        'close_reason_property' => 222,
    ],
    'contact' => [],
];
```

`config/deal-cmd-mapping.php` (маппинг пользовательских команд):

```php
return [
    'принято' => 'accepted',
    'контакт' => 'contact',
    'причина_закрытия' => 'close_reason',
    'причина' => 'reason',
];
```

`config/deal-cmd-messages.php` (тексты сообщений): содержит шаблоны и ошибки, параметры вида `{name}`.

## Расширение: добавление новой команды

1) Реализуйте `CommandInterface` или унаследуйтесь от `AbstractConfigurableCommand`.
2) Зарегистрируйте команду в Laravel провайдере или передайте в список при создании `CommandProcessor`.
3) Добавьте маппинг в `deal-cmd-mapping.php` и при необходимости конфигурацию/сообщения.

## Контракты (главные интерфейсы)

- `DealRepositoryInterface`: работа со свойствами сделки и контактом клиента
- `MessageSenderInterface`: отправка служебных сообщений в сделку
- `LoggerInterface`: логирование выполнения команд
- `CommandConfigurationInterface`: доступ к конфигам команд
- `CommandMapperInterface`: преобразование пользовательских команд в системные
- `MessageProviderInterface`: шаблоны и тексты сообщений

## Тестирование

```bash
composer test
```

В проекте есть юнит- и интеграционные тесты (PHPUnit 10).

## Качество кода

```bash
composer phpcs    # проверка кодстайла (PSR-12)
composer phpstan  # статический анализ
```
