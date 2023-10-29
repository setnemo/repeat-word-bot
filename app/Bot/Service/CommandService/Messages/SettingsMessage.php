<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class SettingsMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class SettingsMessage
{
    public const TEXT_SILENT = 'Тихий режим повідомлень: :silent';
    public const TEXT_PRIORITY = 'Пріоритет меншої ітерації: :priority';
    public const TEXT_CHOICE_VOICE = 'Вибрати голоси для тренувань';
}
