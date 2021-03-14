<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class ProgressMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class ProgressMessage
{
    public const EMPTY_VOCABULARY = 'Ваш словарь пуст. Пожалуйста добавьте коллекцию!';

    const HINT = "`Подсказка:`\nFirst итерация: повтор слова через 24 часа\n" .
        "Second итерация: повтор слова через 3 дня\n" .
        "Third итерация: повтор слова через 7 дней\n" .
        "Fourth итерация: повтор слова через 1 месяц\n" .
        "Fifth итерация: повтор слова через 3 месяца\n" .
        "Sixth итерация: повтор слова через 6 месяца\n" .
        "Never итерация: повтор слова через 1 год\n\n" .
        "`Сброс прогресса:`\nИспользуйте команду `/reset my progress`\n" .
        "Будьте осторожны, сброс не обратим и вам придется начать итерации с начала\n\n" .
        "`Ваша статистика:\n`";
}
