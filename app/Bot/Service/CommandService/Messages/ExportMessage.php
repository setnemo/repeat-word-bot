<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class ExportMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class ExportMessage
{
    public const EXPORT_TEXT = 'Створення експорту поставлено у чергу. Як тільки файл буде готовий, ви отримаєте його в особистому повідомленні.';
    public const ERROR_HAVE_EXPORT_TEXT = 'У вас є експорт слів, дочекайтесь черги для створення файлу';
    public const ERROR_INVALID_PAYLOAD_TEXT = "Допустимі формати команди\n - /export\n - /export FromEnglish first\n" .
    " - /export ToEnglish second\n\n Де перше слово режим без пробілу, а друга назва ітерації. " .
    "Подивитися скільки у вас слів у якій ітерації можна командою /progress";
}
