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

    const HINT = "`Підказка:`\nFirst ітерація: повтор слова через 24 години\n" .
    "Second ітерація: повторення слова через 3 дні\n" .
    "Third ітерація: повторення слова через 7 днів\n" .
    "Fourth ітерація: повторення слова через 1 місяць\n" .
    "Fifth ітерація: повторення слова через 3 місяці\n" .
    "Sixth ітерація: повторення слова через 6 місяці\n" .
    "Never ітерація: повторення слова через 1 рік\n" .
    "`Скинути прогрес:`\nВикористовуйте команду /reset my progress\n" .
    "Будьте обережні, скидання не звернемо і вам доведеться почати ітерації з початку\n\n" .
    "`Ваша статистика:`\n";
}
