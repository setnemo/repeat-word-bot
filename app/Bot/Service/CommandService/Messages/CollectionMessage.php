<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Messages;

/**
 * Class CollectionMessage
 * @package RepeatBot\Bot\Service\CommandService\Messages
 */
class CollectionMessage
{
    public const COLLECTION_WELCOME_TEXT = "Вибирайте колекцію для додавання до свого словника. Слова з колекції будуть доступні у тренуванні.\n\n" .
    "Всі слова розділені на колекції 'за частотою використання' слова в мові." .
    "Не додавайте відразу занадто багато, спочатку відправте на довгі ітерації колекції з популярнішими словами. " .
    "При додаванні Колекції слова додаються в обидва типи тренувань (`From English` + `To English`)." .
    "Також є команда /reset для скидання, якщо ви помилково додали занадто багато або хочете почати спочатку\n\n" .
    "Кожна колекція унікальна! Слова `не повторюються`. На вас чекає пригода на 17814 слів!" .
    "Перші 35 колекцій по 500 слів та в останній 314 слів\n\n" .
    "Слова додаються по 500 штук, тому після натискання кнопки `Додати` дочекайтеся відповіді, що слова додані \n\n" .
    "Листий ⏪ліворуч і праворуч⏩ список слів прикладів буде оновлюватися, це допоможе вам більш точно вибрати колекцію для свого рівня володіння мовою\n\n" .
    "Після додавання будуть доступні кнопки `Видалити` та `Скинути`, які підкажуть команди для видалення колекції або скидання прогресу по даній колекції";
}
