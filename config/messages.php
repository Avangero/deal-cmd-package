<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Сообщения Deal Command Package
    |--------------------------------------------------------------------------
    |
    | Здесь находятся все текстовые сообщения пакета.
    | Вы можете их изменить для кастомизации или локализации.
    | Параметры подставляются в формате {parameter_name}
    |
    */

    // Общие сообщения
    'unknown_command' => 'Неизвестная команда: {command_name}',
    'command_cannot_be_executed' => 'Команда не может быть выполнена с данными аргументами',
    'command_executed_successfully' => 'Команда выполнена успешно',
    'configuration_not_found' => 'Конфигурация для команды \'{command_name}\' не найдена',
    'command_processing_failed' => 'Не удалось обработать команду',

    // Сообщения для Chain handlers
    'command_not_found_or_recognized' => 'Команда не была найдена или не удалось ее распознать',
    'command_not_recognized' => 'Команда не была распознана',
    'command_not_found_or_recognized_validation' => 'Команда не была найдена или не распознана',
    'result_not_found' => 'Результат выполнения команды не найден',
    'command_execution_error' => 'Ошибка выполнения команды',

    // Ошибки парсинга команд
    'command_must_start_with_slash' => 'Команда должна начинаться с символа "/"',
    'command_cannot_be_empty' => 'Команда не может быть пустой',
    'command_parse_failed' => 'Не удалось разобрать команду',

    // Ошибки валидации Value Objects
    'deal_id_must_be_positive' => 'Deal ID должен быть положительным числом',
    'property_id_must_be_positive' => 'Property ID должен быть положительным числом',

    // Команда "принято"
    'accepted_command_requires_two_arguments' => 'Команда "принято" требует два аргумента: сумму и тип',
    'accepted_command_incomplete_config' => 'Неполная конфигурация команды "принято": отсутствуют amount_property или type_property',
    'accepted_command_success' => 'Установлены свойства: #{amount_property} = {amount}, #{type_property} = {type}',

    // Команда "контакт"
    'contact_command_client_not_found' => 'Контактная информация клиента не найдена',
    'contact_command_success' => 'Контактная информация отправлена в сделку',
    'contact_message_template' => 'Контакт клиента: {contact}',

    // Команда "причина_закрытия"
    'close_reason_command_requires_reason' => 'Команда "причина_закрытия" требует указать причину',
    'close_reason_command_incomplete_config' => 'Неполная конфигурация команды "причина_закрытия": отсутствует close_reason_property',
    'close_reason_command_success' => 'Установлена причина закрытия: {reason}',

    // Команда "причина"
    'reason_command_incomplete_config' => 'Неполная конфигурация команды "причина": отсутствует close_reason_property',
    'reason_command_not_set' => 'Причина закрытия не установлена',
    'reason_command_success' => 'Причина закрытия отправлена в сделку',
    'reason_message_template' => 'Причина закрытия: {reason}',
];
