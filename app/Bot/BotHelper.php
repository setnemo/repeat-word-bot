<?php

declare(strict_types=1);

namespace RepeatBot\Bot;

use RepeatBot\Core\Database\Model\Collection;

class BotHelper
{
    /**
     * @return array
     */
    public static function getDefaultKeyboard(): array
    {
        return [
            ['Настройки', 'Справка'],
            ['Мой прогресс', 'Тренировка',],
        ];
    }

    /**
     * @return array
     */
    public static function getInTrainingKeyboard(): array
    {
        return [
            ['Остановить', 'Я не знаю'],
        ];
    }

    /**
     * @return array
     */
    public static function getTrainingKeyboard(): array
    {
        return [
            ['Назад', 'Коллекции слов', 'Мой прогресс', ],
            ['To English', 'From English'],
        ];
    }

    /**
     * @return string[]
     */
    public static function getCommands(): array
    {
        return [
            'Коллекции слов' => 'Collections',
            'Мой прогресс' => 'Progress',
            'From English' => 'VoiceEnglish',
            'FromEnglish' => 'VoiceEnglish',
            'To English' => 'VoiceEnglish',
            'ToEnglish' => 'VoiceEnglish',
            'Настройки' => 'Settings',
            'Тренировка' => 'Training',
            'Справка' => 'Help',
            'Назад' => 'Start',
        ];
    }

    /**
     * @param array $records
     *
     * @return array
     */
    public static function convertCollectionToButton(array $records): array
    {
        $result = $tmp = [];

        /** @var Collection $record */
        foreach ($records as $it => $record) {
            if ((int) $it % 2 == 0) {
                if (!empty($tmp)) {
                    $result[] = $tmp;
                }
                $tmp = [];
                $tmp[] = [
                    'text' => "{$record->getName()}",
                    'callback_data' => "collection_{$record->getId()}"
                ];
            } else {
                $tmp[] = [
                    'text' => "{$record->getName()}",
                    'callback_data' => "collection_{$record->getId()}"
                ];
            }
        }
        if (!empty($tmp)) {
            $result[] = $tmp;
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public static function getTrainingTypes(): array
    {
        return [
            'FromEnglish',
            'ToEnglish',
        ];
    }

    /**
     * @param string $textSilent
     * @param string $textPriority
     * @param int    $silent
     * @param int    $priority
     *
     * @return array
     */
    public static function getSettingsKeyboard(
        string $textSilent,
        string $textPriority,
        int $silent,
        int $priority
    ): array {
        return [
            [
                ['text' => $textSilent, 'callback_data' => "settings_silent_{$silent}"],
            ],[
                ['text' => $textPriority, 'callback_data' => "settings_priority_{$priority}"],
            ]];
    }

    /**
     * @return string
     */
    public static function getCollectionText(): string
    {
        $text = "Выбирайте коллекцию для добавления в свой словарь. Слова с коллекции будут доступны в тренировке.\n\n";
        $text .= "Все слова разделены на коллекции `по частоте использования` слова в языке. ";
        $text .= "Не добавляйте сразу слишком много, сначала отправьте на долгие итерации коллекции с более популярными словами. ";
        $text .= "При добавлении Коллекции слова добавляются в оба типа тренировок (`From English` + `To English`). ";
        $text .= "Также есть команда /reset для сброса, если вы по ошибке добавили слишком много или хотите начать сначала\n\n";
        $text .= "Каждая коллекция уникальна! Слова `не повторяются`. Вас ждет приключение на 17814 слов! ";
        $text .= "Первые 11 коллекций по 1500 слов и в последней 1314 слов\n\n";
        $text .= "Слова добавляются по 500 штук, поэтому после нажатия кнопки `Добавить` дождитесь ответа, что слова добавлены\n\n";
        $text .= "Листая влево и вправо список слов примеров будет обновляться, это поможет вам более точно выбрать коллекцию для своего уровня владения языком\n\n";
        $text .= "После добавления будут доступны кнопки `Удалить` и `Сбросить`, которые подскажут команды для удаления коллекции или сброса прогресса по данной коллекции";

        return $text;
    }

    /**
     * @param string $text
     * @param int    $count
     *
     * @return string
     */
    public static function getAnswer(string $text, int $count): string
    {
        $module = $count > 10 && $count < 15 ? ($count + 5) % 10 : $count % 10;
        $word = match($module) {
            5, 6, 7, 8, 9, 0    => 'слов',
            2, 3, 4,            => 'слова',
            1                   => 'слово',
        };
        $text .= strtr(':count :word', [
            ':count' => $count,
            ':word' => $word,
        ]);

        return $text;
    }

    /**
     * @param int  $collectionNum
     * @param bool $exist
     *
     * @return array
     */
    public static function getCollectionPagination(int $collectionNum, bool $exist): array
    {
        $result[] = BotHelper::getPaginationFw($collectionNum);
        $result[] = BotHelper::getPaginationNums($collectionNum);
        $addRemove = $exist ?
            [
                'text' => "🚫 Удалить",
                'callback_data' => 'ratings_del_' . $collectionNum
            ] :
            [
                'text' => "✅ Добавить",
                'callback_data' => 'ratings_add_' . $collectionNum,
            ];
        $progress = $exist ?
            [
                'text' => "🔄 Сбросить",
                'callback_data' => 'ratings_reset_' . $collectionNum,
            ] :
            [
                'text' => " ",
                'callback_data' => 'empty'
            ];
        $result[] = [
            $progress,
            $addRemove,
        ];
        return $result;
    }

    /**
     * @param int $num
     *
     * @return \string[][]
     */
    private static function getPaginationNums(int $num): array
    {
        return [
            [
                'text' => $num > 1 ? '   ⏮   ' : '        ',
                'callback_data' => $num > 2 ? 'rating_' . 1 : 'empty',
            ],
            [
                'text' => $num > 1 ? '   ⏪   ' : '        ',
                'callback_data' => $num > 1 ? 'rating_' . ($num - 1) : 'empty',
            ],
            [
                'text' => $num < 36 ? '   ⏩   ' : '        ',
                'callback_data' => $num < 36 ? 'rating_' . ($num + 1) : 'empty',
            ],
            [
                'text' => $num < 36 ? '   ⏭   ' : '        ',
                'callback_data' => $num < 36 ? 'rating_' . 36 : 'empty',
            ],
        ];
    }

    /**
     * @param int $num
     *
     * @return \string[][]
     */
    private static function getPaginationFw(int $num): array
    {
        return [
            [
                'text' => $num > 1 ? BotHelper::createEmojiNumber($num - 1) : ' ',
                'callback_data' => $num > 2 ? 'rating_' . ($num - 1) : 'empty',
            ],
            [
                'text' => BotHelper::createEmojiNumber($num),
                'callback_data' => 'rating_' . $num,
            ],
            [
                'text' => $num < 36 ? BotHelper::createEmojiNumber($num + 1) : ' ',
                'callback_data' => $num < 35 ? 'rating_' . ($num + 1) : 'empty',
            ],
        ];
    }

    /**
     * @param int    $num
     * @param string $text
     *
     * @return string
     */
    private static function createEmojiNumber(int $num, string $text = ''): string
    {
        $tmp = $num;
        if ($tmp >= 10) {
            $text .= BotHelper::createEmojiNumber(intval($tmp / 10));
            $text .= BotHelper::createEmojiNumber(intval($tmp % 10));
        }
        if ($tmp < 10) {
            $text .= match($tmp) {
                0 => '0️⃣',
                1 => '1️⃣',
                2 => '2️⃣',
                3 => '3️⃣',
                4 => '4️⃣',
                5 => '5️⃣',
                6 => '6️⃣',
                7 => '7️⃣',
                8 => '8️⃣',
                9 => '9️⃣',
            };
        }
        return $text;
    }

    public static function getSettingsText(): string
    {
        return "`Тихий режим сообщений`:\n" .
            "По умолчанию тихий режим включен для всех. Для переключения режима нажмите на кнопку" .
            " *Тихий режим сообщений*\n\n" .
            "По умолчанию в тренировках выключен приоритет для слов с разных итераций, и они " .
            "показываются в случайно порядке. Если вы хотите сначала проходить слова с меньших итераций, то " .
            "вы можете включить или выключить этот режим нажав на кнопку " .
            " *Приоритет меньших итераций*\n\n";
    }
}
