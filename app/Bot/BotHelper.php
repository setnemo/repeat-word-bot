<?php

declare(strict_types=1);

namespace RepeatBot\Bot;

use Longman\TelegramBot\Entities\InlineKeyboard;
use RepeatBot\Bot\Service\CommandService\Messages\SettingsMessage;
use RepeatBot\Core\ORM\Entities\Training;

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
     * @return string[][]
     */
    public static function getDefaultKeyboard(): array
    {
        return [
            ['Налаштування', 'Довідка'],
            ['Мій прогрес', 'Тренування',],
        ];
    }

    public static function getInTrainingKeyboard(): array
    {
        return [
            ['Зупинити', 'Я не знаю'],
        ];
    }

    /**
     * @return string[][]
     */
    public static function getTrainingKeyboard(): array
    {
        return [
            ['Назад', 'Колекції слів', 'Мій прогрес',],
            ['To English', 'From English'],
        ];
    }

    /**
     * @return string[]
     */
    public static function getCommands(): array
    {
        return [
            'Колекції слів' => 'collections',
            'Мій прогрес'   => 'progress',
            'From English'  => 'translate_training',
            'To English'    => 'translate_training',
            'Налаштування'  => 'settings',
            'Тренування'    => 'training',
            'Зупинити'      => 'training',
            'Я не знаю'     => 'training',
            'Довідка'       => 'help',
            'Назад'         => 'start',
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
     * @return string[]
     */
    public static function getTrainingStatuses(): array
    {
        return [
            'first',
            'second',
            'third',
            'fourth',
            'fifth',
            'sixth',
            'never',
        ];
    }

    /**
     * @param Training $training
     * @param bool $never
     *
     * @return array
     */
    public static function getNewStatus(Training $training, bool $never = false): array
    {
        $status = $never === false ? $training->getStatus() : 'never';

        return match ($status) {
            'second' => [
                'status' => 'third',
                'repeat' => 3 * 24 * 60,
            ],
            'third' => [
                'status' => 'fourth',
                'repeat' => 7 * 24 * 60,
            ],
            'fourth' => [
                'status' => 'fifth',
                'repeat' => 30 * 24 * 60,
            ],
            'fifth' => [
                'status' => 'sixth',
                'repeat' => 90 * 24 * 60,
            ],
            'sixth' => [
                'status' => 'never',
                'repeat' => 180 * 24 * 60,
            ],
            'never' => [
                'status' => 'never',
                'repeat' => 360 * 24 * 60,
            ],
            default => [
                'status' => 'second',
                'repeat' => 24 * 60,
            ],
        };
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
            $key         = self::VOICES[$it]['text'];
            $switcher    = $switchers[$key] == 1 ? '✅' : '❌';
            $switcherNum = $switchers[$key] == 1 ? 0 : 1;
            $voiceName   = str_replace('-', ' ', str_replace('en-US-', '', $key));
            $result[]    = [
                [
                    'text'          => "{$voiceName} {$switcher}",
                    'callback_data' => "settings_voices_{$it}_{$switcherNum}",
                ],
                [
                    'text'          => 'Пример',
                    'callback_data' => "settings_voices_example_{$it}",
                ],
            ];
        }

        $result[] = [
            ['text' => 'Назад', 'callback_data' => "settings_voices_back"],
        ];

        return $result;
    }

    /**
     * @param int $collectionNum
     * @param bool $exist
     *
     * @return array
     */
    public static function getCollectionPagination(int $collectionNum, bool $exist): array
    {
        $result    = [];
        $result[]  = BotHelper::getPaginationFw($collectionNum);
        $result[]  = BotHelper::getPaginationNums($collectionNum);
        $addRemove = $exist ?
            [
                'text'          => "🚫 Видалити",
                'callback_data' => 'collections_del_' . $collectionNum,
            ] :
            [
                'text'          => "✅ Додати",
                'callback_data' => 'collections_add_' . $collectionNum,
            ];
        $progress  = $exist ?
            [
                'text'          => "🔄 Скинути",
                'callback_data' => 'collections_reset_' . $collectionNum,
            ] :
            [
                'text'          => " ",
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
     * @return array[]
     */
    private static function getPaginationFw(int $num): array
    {
        return [
            [
                'text'          => $num > 1 ? BotHelper::createEmojiNumber($num - 1) : ' ',
                'callback_data' => $num > 1 ? 'collections_' . ($num - 1) : 'empty',
            ],
            [
                'text'          => BotHelper::createEmojiNumber($num),
                'callback_data' => 'collections_' . $num,
            ],
            [
                'text'          => $num < 36 ? BotHelper::createEmojiNumber($num + 1) : ' ',
                'callback_data' => $num < 36 ? 'collections_' . ($num + 1) : 'empty',
            ],
        ];
    }

    /**
     * @param int $num
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
            $text .= match ($tmp) {
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
     * @param int $num
     *
     * @return string[][]
     */
    private static function getPaginationNums(int $num): array
    {
        return [
            [
                'text'          => $num > 1 ? '   ⏮   ' : '        ',
                'callback_data' => $num > 1 ? 'collections_' . 1 : 'empty',
            ],
            [
                'text'          => $num > 1 ? '   ⏪   ' : '        ',
                'callback_data' => $num > 1 ? 'collections_' . ($num - 1) : 'empty',
            ],
            [
                'text'          => $num < 36 ? '   ⏩   ' : '        ',
                'callback_data' => $num < 36 ? 'collections_' . ($num + 1) : 'empty',
            ],
            [
                'text'          => $num < 36 ? '   ⏭   ' : '        ',
                'callback_data' => $num < 36 ? 'collections_' . 36 : 'empty',
            ],
        ];
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
        $symbolSilent   = $silent === 1 ? '✅' : '❌';
        $symbolPriority = $priority === 1 ? '✅' : '❌';
        $textSilent     = strtr(SettingsMessage::TEXT_SILENT, [':silent' => $symbolSilent]);
        $texPriority    = strtr(SettingsMessage::TEXT_PRIORITY, [':priority' => $symbolPriority]);
        $texVoices      = SettingsMessage::TEXT_CHOICE_VOICE;
        /** @psalm-suppress TooManyArguments */
        $keyboard = new InlineKeyboard(
            ...BotHelper::getSettingsKeyboard(
                $textSilent,
                $texPriority,
                $texVoices,
                $silent === 1 ? 0 : 1,
                $priority === 1 ? 0 : 1,
            )
        );
        return [
            'chat_id'      => $user_id,
            'text'         => BotHelper::getSettingsText(),
            'reply_markup' => $keyboard,
            'message_id'   => $message_id,
            'parse_mode'   => 'markdown',
        ];
    }

    /**
     * @param string $textSilent
     * @param string $textPriority
     * @param string $textVoices
     * @param int $silent
     * @param int $priority
     *
     * @return string[][][]
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
            ],
        ];
    }

    /**
     * @return string
     */
    public static function getSettingsText(): string
    {
        return "`Тихий режим повідомлень`:\n" .
            "За промовчанням тихий режим увімкнено для всіх. Щоб переключити режим, натисніть кнопку" .
            "*Тихий режим повідомлень*\n\n" .
            "`Пріоритет менших ітерацій`:\nЗа замовчуванням у тренуваннях вимкнено пріоритет для слів з різних ітерацій, і вони " .
            "показуються у випадковому порядку. Якщо ви хочете спочатку проходити слова з менших ітерацій, то " .
            "Ви можете ввімкнути або вимкнути цей режим, натиснувши кнопку " .
            "*Пріоритет менших ітерацій*\n\n" .
            "`Голоси для тренувань`:\n" .
            "За замовчуванням завжди включено один стандартний голос en-US-Wavenet-A. Для отримання більшого" .
            "досвід прослуховування різних варіантів вимови ви можете включити до 9 додаткових голосів, один " .
            "з яких вибиратиметься випадково при кожному слові у тренуваннях";
    }

    /**
     * @return string
     */
    public static function getTimeText(): string
    {
        $text      = "Список аббривіатур, що підтримуються, для вибору часового поясу в персональних нагадуваннях:\n\n";
        $timezones = BotHelper::getTimeZones();
        foreach ($timezones as $timezone) {
            $text .= strtr("`:abbr:` :text\n", [
                ':abbr' => $timezone['abbr'],
                ':text' => $timezone['text'],
            ]);
        }

        return $text . "\nДля нагадування використовуйте літерний код, наприклад FDT (FLE Standard Time - Kyiv), тоді команда буде /alarm FDT 9:00";
    }

    /**
     * @return array
     */
    public static function getTimeZones(): array
    {
        return include '/app/config/timezones.php';
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

    /**
     * @param array $records
     * @param string $text
     *
     * @return string
     */
    public static function getProgressText(array $records, string $text): string
    {
        foreach ($records as $type => $items) {
            foreach ($items as $item) {
                $status = ucfirst($item['status']);
                $text   .= BotHelper::getAnswer(
                    "\[{$type}] {$status} ітерація: ",
                    (int)$item['counter']
                ) . "\n";
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @param int $count
     *
     * @return string
     */
    public static function getAnswer(string $text, int $count): string
    {
        $module = $count > 10 && $count < 15 ? ($count + 5) % 10 : $count % 10;
        $word   = match ($module) {
            5, 6, 7, 8, 9, 0 => 'слів',
            2, 3, 4, => 'слова',
            1 => 'слово',
        };
        $text   .= strtr(':count :word', [
            ':count' => $count,
            ':word'  => $word,
        ]);

        return $text;
    }
}
