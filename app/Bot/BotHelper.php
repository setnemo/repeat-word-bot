<?php

declare(strict_types=1);

namespace RepeatBot\Bot;

use JetBrains\PhpStorm\ArrayShape;
use Longman\TelegramBot\Entities\InlineKeyboard;

class BotHelper
{
    protected const VOICES = [
        ['text' => 'en-US-Wavenet-A'],
        ['text' => 'en-US-Wavenet-B'],
        ['text' => 'en-US-Wavenet-C'],
        ['text' => 'en-US-Wavenet-D'],
        ['text' => 'en-US-Wavenet-E'],
        ['text' => 'en-US-Wavenet-F'],
        ['text' => 'en-US-Wavenet-G'],
        ['text' => 'en-US-Wavenet-H'],
        ['text' => 'en-US-Wavenet-I'],
        ['text' => 'en-US-Wavenet-J'],
    ];

    /**
     * @return array
     */
    public static function getVoices(): array
    {
        return array_column(self::VOICES, 'text');
    }

    /**
     * @return \string[][]
     */
    public static function getDefaultKeyboard(): array
    {
        return [
            ['Настройки', 'Справка'],
            ['Мой прогресс', 'Тренировка',],
        ];
    }

    public static function getInTrainingKeyboard(): array
    {
        return [
            ['Остановить', 'Я не знаю'],
        ];
    }

    /**
     * @return \string[][]
     */
    public static function getTrainingKeyboard(): array
    {
        return [
            ['Назад', 'Коллекции слов', 'Мой прогресс',],
            ['To English', 'From English'],
        ];
    }

    /**
     * @return string[]
     */
    public static function getCommands(): array
    {
        return [
            'Коллекции слов' => 'collections',
            'Мой прогресс' => 'progress',
            'From English' => 'translate_training',
            'To English' => 'translate_training',
            'Настройки' => 'settings',
            'Тренировка' => 'training',
            'Остановить' => 'training',
            'Я не знаю' => 'training',
            'Справка' => 'help',
            'Назад' => 'start',
        ];
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
     * @param string $textVoices
     * @param int    $silent
     * @param int    $priority
     *
     * @return \string[][][]
     */
    public static function getSettingsKeyboard(
        string $textSilent,
        string $textPriority,
        string $textVoices,
        int $silent,
        int $priority
    ): array {
        return [
            [
                ['text' => $textSilent, 'callback_data' => "settings_silent_{$silent}"],
            ],
            [
                ['text' => $textPriority, 'callback_data' => "settings_priority_{$priority}"],
            ],
            [
                ['text' => $textVoices, 'callback_data' => "settings_voices_start"],
            ]
        ];
    }

    /**
     * @param array $switchers
     *
     * @return array
     */
    public static function getSettingsVoicesKeyboard(array $switchers): array
    {
        $result = [];

        foreach (self::VOICES as $it => $value) {
            $key =  self::VOICES[$it]['text'];
            $switcher = $switchers[$key] == 1 ? '✅' : '❌';
            $switcherNum = $switchers[$key] == 1 ? 0 : 1;
            $voiceName = str_replace('-', ' ', str_replace('en-US-', '', $key));
            $result[] = [
                [
                    'text' => "{$voiceName} {$switcher}",
                    'callback_data' => "settings_voices_{$it}_{$switcherNum}"
                ],
                [
                    'text' => 'Пример',
                    'callback_data' => "settings_voices_example_{$it}"
                ],
            ];
        }

        $result[] = [
            ['text' => 'Назад', 'callback_data' => "settings_voices_back"],
        ];

        return $result;
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
        $text .= "Первые 35 коллекций по 500 слов и в последней 314 слов\n\n";
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
            5, 6, 7, 8, 9, 0 => 'слов',
            2, 3, 4, => 'слова',
            1 => 'слово',
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
        $result = [];
        $result[] = BotHelper::getPaginationFw($collectionNum);
        $result[] = BotHelper::getPaginationNums($collectionNum);
        $addRemove = $exist ?
            [
                'text' => "🚫 Удалить",
                'callback_data' => 'collections_del_' . $collectionNum,
            ] :
            [
                'text' => "✅ Добавить",
                'callback_data' => 'collections_add_' . $collectionNum,
            ];
        $progress = $exist ?
            [
                'text' => "🔄 Сбросить",
                'callback_data' => 'collections_reset_' . $collectionNum,
            ] :
            [
                'text' => " ",
                'callback_data' => 'empty',
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
                'callback_data' => $num > 1 ? 'collections_' . 1 : 'empty',
            ],
            [
                'text' => $num > 1 ? '   ⏪   ' : '        ',
                'callback_data' => $num > 1 ? 'collections_' . ($num - 1) : 'empty',
            ],
            [
                'text' => $num < 36 ? '   ⏩   ' : '        ',
                'callback_data' => $num < 36 ? 'collections_' . ($num + 1) : 'empty',
            ],
            [
                'text' => $num < 36 ? '   ⏭   ' : '        ',
                'callback_data' => $num < 36 ? 'collections_' . 36 : 'empty',
            ],
        ];
    }

    /**
     * @param int $num
     *
     * @return array[]
     */
    private static function getPaginationFw(int $num): array
    {
        return [
            [
                'text' => $num > 1 ? BotHelper::createEmojiNumber($num - 1) : ' ',
                'callback_data' => $num > 1 ? 'collections_' . ($num - 1) : 'empty',
            ],
            [
                'text' => BotHelper::createEmojiNumber($num),
                'callback_data' => 'collections_' . $num,
            ],
            [
                'text' => $num < 36 ? BotHelper::createEmojiNumber($num + 1) : ' ',
                'callback_data' => $num < 36 ? 'collections_' . ($num + 1) : 'empty',
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

    /**
     * @param int $silent
     * @param int $priority
     * @param int $user_id
     * @param int $message_id
     *
     * @return array
     */
    public static function editMainMenuSettings(int $silent, int $priority, int $user_id, int $message_id): array
    {
        $symbolSilent = $silent === 1 ? '✅' : '❌';
        $symbolPriority = $priority === 1 ? '✅' : '❌';
        $textSilent = "Тихий режим сообщений: {$symbolSilent}";
        $texPriority = "Приоритет меньшей итерации: {$symbolPriority}";
        $texVoices = "Выбрать голоса для тренировок";
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(...BotHelper::getSettingsKeyboard(
            $textSilent,
            $texPriority,
            $texVoices,
            $silent === 1 ? 0 : 1,
            $priority === 1 ? 0 : 1,
        ));
        return [
            'chat_id' => $user_id,
            'text' => BotHelper::getSettingsText(),
            'reply_markup' => $keyboard,
            'message_id' => $message_id,
            'parse_mode' => 'markdown',
        ];
    }

    /**
     * @return string
     */
    public static function getSettingsText(): string
    {
        return "`Тихий режим сообщений`:\n" .
        "По умолчанию тихий режим включен для всех. Для переключения режима нажмите на кнопку" .
        " *Тихий режим сообщений*\n\n" .
        "`Приоритет меньших итераций`:\nПо умолчанию в тренировках выключен приоритет для слов с разных итераций, и они " .
        "показываются в случайно порядке. Если вы хотите сначала проходить слова с меньших итераций, то " .
        "вы можете включить или выключить этот режим нажав на кнопку " .
        " *Приоритет меньших итераций*\n\n" .
        "`Голоса для тренировок`:\n" .
        "По умолчанию всегда включен один стандартный голос en-US-Wavenet-A. Для получения бОльшего " .
        "опыта в прослушивании разных вариантов произношения вы можете включить до 9 дополнительных голосов, один " .
        "из которых будет выбираться случайно при каждом слове в тренировках";
    }

    /**
     * @return array
     */
    public static function getTimeZones(): array
    {
        return include_once __DIR__ . '/../../config/timezones.php';
    }

    /**
     * @param string|null $input
     *
     * @return string
     */
    public static function getTextFromInput(?string $input): string
    {
        return null === $input ? '' : $input;
    }
}
