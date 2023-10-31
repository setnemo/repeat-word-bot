<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class AlarmMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class AlarmMessage
{
    public const ERROR_TEXT = "Щоб створити нагадування на 9 ранку - скористайтесь командою `/alarm 9:00`." .
    "Щоб створити нагадування на 9 вечора - скористайтесь командою `/alarm 21:00`.\n\n" .
    "За замовчуванням використовується часовий пояс FLE Standard Time (Kyiv), тобто насправді команди вище насправді" .
    "можна відправити з кодом FDT `/alarm FDT 9:00` і результат буде той самий. Якщо ж вам потрібно отримувати" .
    "повідомлення по іншому часовому поясу, наприклад BRST, то потрібно писати так `/alarm BRST 9:00`\n\n" .
    "Переглянути всі коди часових поясів можна за допомогою команди /time\n\n" .
    "Подивитися свої нагадування /alarm list\n\n" .
    "Видалити свої нагадування /alarm reset";
}
