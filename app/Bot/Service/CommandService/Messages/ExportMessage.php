<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class ExportMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class ExportMessage
{
    public const EXPORT_TEXT = 'Создание экспорта поставлено в очередь. Как только файл будет готов вы получите его в личном сообщении.';
    public const ERROR_HAVE_EXPORT_TEXT = 'У вас есть экспорт слов, дождитесь очереди для создания файла';
    public const ERROR_INVALID_PAYLOAD_TEXT = "Допустимые форматы команды\n - /export\n - /export FromEnglish first\n" .
    " - /export ToEnglish second\n\n Где первое слово режим без пробела, а второе название итерации. " .
    "Посмотреть сколько у вас слов в какой итерации можно командой /progress";
}
