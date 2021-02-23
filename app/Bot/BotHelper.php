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
            'Мой прогресс' => 'MyVocabulary',
            'From English' => 'VoiceEnglish',
            'FromEnglish' => 'VoiceEnglish',
            'To English' => 'VoiceEnglish',
            'ToEnglish' => 'VoiceEnglish',
            'Настройки' => 'Settings',
            'Тренировка' => 'StartTraining',
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
     * @param string $text
     * @param int    $switch
     *
     * @return array
     */
    public static function getSettingsKeyboard(string $text, int $switch): array
    {
        return [
            ['text' => $text, 'callback_data' => "settings_silent_{$switch}"],
        ];
    }

    /**
     * @return string
     */
    public static function getCollectionText(): string
    {
        $text = "Выбирайте коллекцию для добавления в свой словарь. Слова с коллекции будут доступны в тренировке.\n\n";
        $text .= "Все слова разделены на коллекции по частоте использования слова в языке. ";
        $text .= "Не добавляйте сразу слишком много, сначала отправьте на долгие итерации коллекции с более популярными словами. ";
        $text .= "При добавлении Коллекции слова добавляются в оба типа тренировок (From English + To English). ";
        $text .= "Также есть команда /reset для сброса, если вы по ошибке добавили слишком много или хотите начать сначала\n\n";
        $text .= "Каждая коллекция уникальна! Слова НЕ ПОВТОРЯЮТСЯ. Вас ждет приключение на 17814 слов! ";
        $text .= "Первые 11 коллекций по 1500 слов и в последней 1314 слов";

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
            1 => 'слово',
            2, 3, 4, => 'слова',
            5, 6, 7, 8, 9, 0 => 'слов',
        };
        $text .= "{$count} {$word}";

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
        $result[] = BotHelper::getPagination($collectionNum);
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
    private static function getPagination(int $num): array
    {
        $emoji = BotHelper::createEmojiNumber($num);
        return [
            [
                'text' => $num > 1 ? '   ⏪   ' : '        ',
                'callback_data' => $num > 1 ? 'rating_' . ($num - 1) : 'empty',
            ],
            [
                'text' => "   {$emoji}   ",
                'callback_data' => 'empty',
            ],
            [
                'text' => $num < 12 ? '   ⏩   ' : '        ',
                'callback_data' => $num < 12 ? 'rating_' . ($num + 1) : 'empty',
            ],
        ];
    }

    private static function createEmojiNumber(int $num, string $text = '')
    {
        $m = $num;
        if ($m >= 10) {
            $text .= BotHelper::createEmojiNumber(intval($m / 10));
            $m -= 10;
        }
        if ($m < 10) {
            $text .= match($m) {
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
}
