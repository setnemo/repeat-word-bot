<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class SettingsMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class SettingsMessage
{
    public const TEXT_SILENT = 'Тихий режим сообщений: :silent';
    public const TEXT_PRIORITY = 'Приоритет меньшей итерации: :priority';
    public const TEXT_CHOICE_VOICE = 'Выбрать голоса для тренировок';
}
