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
                    'callback_data' => "collection_{$record->getId()}",
                ];
            } else {
                $tmp[] = [
                    'text' => "{$record->getName()}",
                    'callback_data' => "collection_{$record->getId()}",
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
            ],
            [
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
        $result[] = BotHelper::getPaginationFw($collectionNum);
        $result[] = BotHelper::getPaginationNums($collectionNum);
        $addRemove = $exist ?
            [
                'text' => "🚫 Удалить",
                'callback_data' => 'ratings_del_' . $collectionNum,
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

    public static function getTimeZones(): array
    {
        return [
            [
                'value' => 'Dateline Standard Time',
                'abbr' => 'DST',
                'offset' => -12,
                'isdst' => false,
                'text' => '(UTC-12:00) International Date Line West',
                'utc' =>
                    [
                        'Etc/GMT+12',
                    ],
            ],
            [
                'value' => 'UTC-11',
                'abbr' => 'UTC-11',
                'offset' => -11,
                'isdst' => false,
                'text' => '(UTC-11:00) Coordinated Universal Time-11',
                'utc' =>
                    [
                        'Etc/GMT+11',
                        'Pacific/Midway',
                        'Pacific/Niue',
                        'Pacific/Pago_Pago',
                    ],
            ],
            [
                'value' => 'Hawaiian Standard Time',
                'abbr' => 'HST',
                'offset' => -10,
                'isdst' => false,
                'text' => '(UTC-10:00) Hawaii',
                'utc' =>
                    [
                        'Etc/GMT+10',
                        'Pacific/Honolulu',
                        'Pacific/Johnston',
                        'Pacific/Rarotonga',
                        'Pacific/Tahiti',
                    ],
            ],
            [
                'value' => 'Alaskan Standard Time',
                'abbr' => 'AKDT',
                'offset' => -8,
                'isdst' => true,
                'text' => '(UTC-09:00) Alaska',
                'utc' =>
                    [
                        'America/Anchorage',
                        'America/Juneau',
                        'America/Nome',
                        'America/Sitka',
                        'America/Yakutat',
                    ],
            ],
            [
                'value' => 'Pacific Daylight Time',
                'abbr' => 'PDT',
                'offset' => -7,
                'isdst' => true,
                'text' => '(UTC-07:00) Pacific Time (US & Canada)',
                'utc' =>
                    [
                        'America/Dawson',
                        'America/Los_Angeles',
                        'America/Tijuana',
                        'America/Vancouver',
                        'America/Whitehorse',
                    ],
            ],
            [
                'value' => 'Pacific Standard Time',
                'abbr' => 'PST',
                'offset' => -8,
                'isdst' => false,
                'text' => '(UTC-08:00) Pacific Time (US & Canada)',
                'utc' =>
                    [
                        'America/Dawson',
                        'America/Los_Angeles',
                        'America/Tijuana',
                        'America/Vancouver',
                        'America/Whitehorse',
                        'PST8PDT',
                    ],
            ],
            [
                'value' => 'US Mountain Standard Time',
                'abbr' => 'UMST',
                'offset' => -7,
                'isdst' => false,
                'text' => '(UTC-07:00) Arizona',
                'utc' =>
                    [
                        'America/Creston',
                        'America/Dawson_Creek',
                        'America/Hermosillo',
                        'America/Phoenix',
                        'Etc/GMT+7',
                    ],
            ],
            [
                'value' => 'Mountain Standard Time',
                'abbr' => 'MDT',
                'offset' => -6,
                'isdst' => true,
                'text' => '(UTC-07:00) Mountain Time (US & Canada)',
                'utc' =>
                    [
                        'America/Boise',
                        'America/Cambridge_Bay',
                        'America/Denver',
                        'America/Edmonton',
                        'America/Inuvik',
                        'America/Ojinaga',
                        'America/Yellowknife',
                        'MST7MDT',
                    ],
            ],
            [
                'value' => 'Central America Standard Time',
                'abbr' => 'CAST',
                'offset' => -6,
                'isdst' => false,
                'text' => '(UTC-06:00) Central America',
                'utc' =>
                    [
                        'America/Belize',
                        'America/Costa_Rica',
                        'America/El_Salvador',
                        'America/Guatemala',
                        'America/Managua',
                        'America/Tegucigalpa',
                        'Etc/GMT+6',
                        'Pacific/Galapagos',
                    ],
            ],
            [
                'value' => 'Central Standard Time',
                'abbr' => 'CDT',
                'offset' => -5,
                'isdst' => true,
                'text' => '(UTC-06:00) Central Time (US & Canada)',
                'utc' =>
                    [
                        'America/Chicago',
                        'America/Indiana/Knox',
                        'America/Indiana/Tell_City',
                        'America/Matamoros',
                        'America/Menominee',
                        'America/North_Dakota/Beulah',
                        'America/North_Dakota/Center',
                        'America/North_Dakota/New_Salem',
                        'America/Rainy_River',
                        'America/Rankin_Inlet',
                        'America/Resolute',
                        'America/Winnipeg',
                        'CST6CDT',
                    ],
            ],
            [
                'value' => 'Canada Central Standard Time',
                'abbr' => 'CCST',
                'offset' => -6,
                'isdst' => false,
                'text' => '(UTC-06:00) Saskatchewan',
                'utc' =>
                    [
                        'America/Regina',
                        'America/Swift_Current',
                    ],
            ],
            [
                'value' => 'SA Pacific Standard Time',
                'abbr' => 'SPST',
                'offset' => -5,
                'isdst' => false,
                'text' => '(UTC-05:00) Bogota, Lima, Quito',
                'utc' =>
                    [
                        'America/Bogota',
                        'America/Cayman',
                        'America/Coral_Harbour',
                        'America/Eirunepe',
                        'America/Guayaquil',
                        'America/Jamaica',
                        'America/Lima',
                        'America/Panama',
                        'America/Rio_Branco',
                        'Etc/GMT+5',
                    ],
            ],
            [
                'value' => 'Eastern Standard Time',
                'abbr' => 'EDT',
                'offset' => -4,
                'isdst' => true,
                'text' => '(UTC-05:00) Eastern Time (US & Canada)',
                'utc' =>
                    [
                        'America/Detroit',
                        'America/Havana',
                        'America/Indiana/Petersburg',
                        'America/Indiana/Vincennes',
                        'America/Indiana/Winamac',
                        'America/Iqaluit',
                        'America/Kentucky/Monticello',
                        'America/Louisville',
                        'America/Montreal',
                        'America/Nassau',
                        'America/New_York',
                        'America/Nipigon',
                        'America/Pangnirtung',
                        'America/Port-au-Prince',
                        'America/Thunder_Bay',
                        'America/Toronto',
                        'EST5EDT',
                    ],
            ],
            [
                'value' => 'US Eastern Standard Time',
                'abbr' => 'UEDT',
                'offset' => -4,
                'isdst' => true,
                'text' => '(UTC-05:00) Indiana (East)',
                'utc' =>
                    [
                        'America/Indiana/Marengo',
                        'America/Indiana/Vevay',
                        'America/Indianapolis',
                    ],
            ],
            [
                'value' => 'Venezuela Standard Time',
                'abbr' => 'AVST',
                'offset' => -4.5,
                'isdst' => false,
                'text' => '(UTC-04:30) Caracas',
                'utc' =>
                    [
                        'America/Caracas',
                    ],
            ],
            [
                'value' => 'Paraguay Standard Time',
                'abbr' => 'PYT',
                'offset' => -4,
                'isdst' => false,
                'text' => '(UTC-04:00) Asuncion',
                'utc' =>
                    [
                        'America/Asuncion',
                    ],
            ],
            [
                'value' => 'Atlantic Standard Time',
                'abbr' => 'ADT',
                'offset' => -3,
                'isdst' => true,
                'text' => '(UTC-04:00) Atlantic Time (Canada)',
                'utc' =>
                    [
                        'America/Glace_Bay',
                        'America/Goose_Bay',
                        'America/Halifax',
                        'America/Moncton',
                        'America/Thule',
                        'Atlantic/Bermuda',
                    ],
            ],
            [
                'value' => 'Central Brazilian Standard Time',
                'abbr' => 'CBST',
                'offset' => -4,
                'isdst' => false,
                'text' => '(UTC-04:00) Cuiaba',
                'utc' =>
                    [
                        'America/Campo_Grande',
                        'America/Cuiaba',
                    ],
            ],
            [
                'value' => 'SA Western Standard Time',
                'abbr' => 'SWST',
                'offset' => -4,
                'isdst' => false,
                'text' => '(UTC-04:00) Georgetown, La Paz, Manaus, San Juan',
                'utc' =>
                    [
                        'America/Anguilla',
                        'America/Antigua',
                        'America/Aruba',
                        'America/Barbados',
                        'America/Blanc-Sablon',
                        'America/Boa_Vista',
                        'America/Curacao',
                        'America/Dominica',
                        'America/Grand_Turk',
                        'America/Grenada',
                        'America/Guadeloupe',
                        'America/Guyana',
                        'America/Kralendijk',
                        'America/La_Paz',
                        'America/Lower_Princes',
                        'America/Manaus',
                        'America/Marigot',
                        'America/Martinique',
                        'America/Montserrat',
                        'America/Port_of_Spain',
                        'America/Porto_Velho',
                        'America/Puerto_Rico',
                        'America/Santo_Domingo',
                        'America/St_Barthelemy',
                        'America/St_Kitts',
                        'America/St_Lucia',
                        'America/St_Thomas',
                        'America/St_Vincent',
                        'America/Tortola',
                        'Etc/GMT+4',
                    ],
            ],
            [
                'value' => 'Pacific SA Standard Time',
                'abbr' => 'PSST',
                'offset' => -4,
                'isdst' => false,
                'text' => '(UTC-04:00) Santiago',
                'utc' =>
                    [
                        'America/Santiago',
                        'Antarctica/Palmer',
                    ],
            ],
            [
                'value' => 'Newfoundland Standard Time',
                'abbr' => 'NDT',
                'offset' => -2.5,
                'isdst' => true,
                'text' => '(UTC-03:30) Newfoundland',
                'utc' =>
                    [
                        'America/St_Johns',
                    ],
            ],
            [
                'value' => 'E. South America Standard Time',
                'abbr' => 'ESAST',
                'offset' => -3,
                'isdst' => false,
                'text' => '(UTC-03:00) Brasilia',
                'utc' =>
                    [
                        'America/Sao_Paulo',
                    ],
            ],
            [
                'value' => 'Argentina Standard Time',
                'abbr' => 'ARSDT',
                'offset' => -3,
                'isdst' => false,
                'text' => '(UTC-03:00) Buenos Aires',
                'utc' =>
                    [
                        'America/Argentina/La_Rioja',
                        'America/Argentina/Rio_Gallegos',
                        'America/Argentina/Salta',
                        'America/Argentina/San_Juan',
                        'America/Argentina/San_Luis',
                        'America/Argentina/Tucuman',
                        'America/Argentina/Ushuaia',
                        'America/Buenos_Aires',
                        'America/Catamarca',
                        'America/Cordoba',
                        'America/Jujuy',
                        'America/Mendoza',
                    ],
            ],
            [
                'value' => 'SA Eastern Standard Time',
                'abbr' => 'SEST',
                'offset' => -3,
                'isdst' => false,
                'text' => '(UTC-03:00) Cayenne, Fortaleza',
                'utc' =>
                    [
                        'America/Araguaina',
                        'America/Belem',
                        'America/Cayenne',
                        'America/Fortaleza',
                        'America/Maceio',
                        'America/Paramaribo',
                        'America/Recife',
                        'America/Santarem',
                        'Antarctica/Rothera',
                        'Atlantic/Stanley',
                        'Etc/GMT+3',
                    ],
            ],
            [
                'value' => 'Greenland Standard Time',
                'abbr' => 'GRDT',
                'offset' => -3,
                'isdst' => true,
                'text' => '(UTC-03:00) Greenland',
                'utc' =>
                    [
                        'America/Godthab',
                    ],
            ],
            [
                'value' => 'Bahia Standard Time',
                'abbr' => 'BST',
                'offset' => -3,
                'isdst' => false,
                'text' => '(UTC-03:00) Salvador',
                'utc' =>
                    [
                        'America/Bahia',
                    ],
            ],
            [
                'value' => 'UTC-02',
                'abbr' => 'U',
                'offset' => -2,
                'isdst' => false,
                'text' => '(UTC-02:00) Coordinated Universal Time-02',
                'utc' =>
                    [
                        'America/Noronha',
                        'Atlantic/South_Georgia',
                        'Etc/GMT+2',
                    ],
            ],
            [
                'value' => 'Cape Verde Standard Time',
                'abbr' => 'CVST',
                'offset' => -1,
                'isdst' => false,
                'text' => '(UTC-01:00) Cape Verde Is.',
                'utc' =>
                    [
                        'Atlantic/Cape_Verde',
                        'Etc/GMT+1',
                    ],
            ],
            [
                'value' => 'Morocco Standard Time',
                'abbr' => 'MSDT',
                'offset' => 1,
                'isdst' => true,
                'text' => '(UTC) Casablanca',
                'utc' =>
                    [
                        'Africa/Casablanca',
                        'Africa/El_Aaiun',
                    ],
            ],
            [
                'value' => 'UTC',
                'abbr' => 'UTC',
                'offset' => 0,
                'isdst' => false,
                'text' => '(UTC) Coordinated Universal Time',
                'utc' =>
                    [
                        'America/Danmarkshavn',
                        'Etc/GMT',
                    ],
            ],
            [
                'value' => 'GMT Standard Time',
                'abbr' => 'GMT',
                'offset' => 0,
                'isdst' => false,
                'text' => '(UTC) Edinburgh, London',
                'utc' =>
                    [
                        'Europe/Isle_of_Man',
                        'Europe/Guernsey',
                        'Europe/Jersey',
                        'Europe/London',
                    ],
            ],
            [
                'value' => 'British Summer Time',
                'abbr' => 'BRST',
                'offset' => 1,
                'isdst' => true,
                'text' => '(UTC+01:00) Edinburgh, London',
                'utc' =>
                    [
                        'Europe/Isle_of_Man',
                        'Europe/Guernsey',
                        'Europe/Jersey',
                        'Europe/London',
                    ],
            ],
            [
                'value' => 'GMT Standard Time',
                'abbr' => 'GDT',
                'offset' => 1,
                'isdst' => true,
                'text' => '(UTC) Dublin, Lisbon',
                'utc' =>
                    [
                        'Atlantic/Canary',
                        'Atlantic/Faeroe',
                        'Atlantic/Madeira',
                        'Europe/Dublin',
                        'Europe/Lisbon',
                    ],
            ],
            [
                'value' => 'Greenwich Standard Time',
                'abbr' => 'GST',
                'offset' => 0,
                'isdst' => false,
                'text' => '(UTC) Monrovia, Reykjavik',
                'utc' =>
                    [
                        'Africa/Abidjan',
                        'Africa/Accra',
                        'Africa/Bamako',
                        'Africa/Banjul',
                        'Africa/Bissau',
                        'Africa/Conakry',
                        'Africa/Dakar',
                        'Africa/Freetown',
                        'Africa/Lome',
                        'Africa/Monrovia',
                        'Africa/Nouakchott',
                        'Africa/Ouagadougou',
                        'Africa/Sao_Tome',
                        'Atlantic/Reykjavik',
                        'Atlantic/St_Helena',
                    ],
            ],
            [
                'value' => 'W. Europe Standard Time',
                'abbr' => 'WEDT',
                'offset' => 2,
                'isdst' => true,
                'text' => '(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
                'utc' =>
                    [
                        'Arctic/Longyearbyen',
                        'Europe/Amsterdam',
                        'Europe/Andorra',
                        'Europe/Berlin',
                        'Europe/Busingen',
                        'Europe/Gibraltar',
                        'Europe/Luxembourg',
                        'Europe/Malta',
                        'Europe/Monaco',
                        'Europe/Oslo',
                        'Europe/Rome',
                        'Europe/San_Marino',
                        'Europe/Stockholm',
                        'Europe/Vaduz',
                        'Europe/Vatican',
                        'Europe/Vienna',
                        'Europe/Zurich',
                    ],
            ],
            [
                'value' => 'Central Europe Standard Time',
                'abbr' => 'CEDT',
                'offset' => 2,
                'isdst' => true,
                'text' => '(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
                'utc' =>
                    [
                        'Europe/Belgrade',
                        'Europe/Bratislava',
                        'Europe/Budapest',
                        'Europe/Ljubljana',
                        'Europe/Podgorica',
                        'Europe/Prague',
                        'Europe/Tirane',
                    ],
            ],
            [
                'value' => 'Romance Standard Time',
                'abbr' => 'RDT',
                'offset' => 2,
                'isdst' => true,
                'text' => '(UTC+01:00) Brussels, Copenhagen, Madrid, Paris',
                'utc' =>
                    [
                        'Africa/Ceuta',
                        'Europe/Brussels',
                        'Europe/Copenhagen',
                        'Europe/Madrid',
                        'Europe/Paris',
                    ],
            ],
            [
                'value' => 'W. Central Africa Standard Time',
                'abbr' => 'WCAST',
                'offset' => 1,
                'isdst' => false,
                'text' => '(UTC+01:00) West Central Africa',
                'utc' =>
                    [
                        'Africa/Algiers',
                        'Africa/Bangui',
                        'Africa/Brazzaville',
                        'Africa/Douala',
                        'Africa/Kinshasa',
                        'Africa/Lagos',
                        'Africa/Libreville',
                        'Africa/Luanda',
                        'Africa/Malabo',
                        'Africa/Ndjamena',
                        'Africa/Niamey',
                        'Africa/Porto-Novo',
                        'Africa/Tunis',
                        'Etc/GMT-1',
                    ],
            ],
            [
                'value' => 'Namibia Standard Time',
                'abbr' => 'NASDT',
                'offset' => 1,
                'isdst' => false,
                'text' => '(UTC+01:00) Windhoek',
                'utc' =>
                    [
                        'Africa/Windhoek',
                    ],
            ],
            [
                'value' => 'GTB Standard Time',
                'abbr' => 'GEDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) Athens, Bucharest',
                'utc' =>
                    [
                        'Asia/Nicosia',
                        'Europe/Athens',
                        'Europe/Bucharest',
                        'Europe/Chisinau',
                    ],
            ],
            [
                'value' => 'Middle East Standard Time',
                'abbr' => 'MEDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) Beirut',
                'utc' =>
                    [
                        'Asia/Beirut',
                    ],
            ],
            [
                'value' => 'Egypt Standard Time',
                'abbr' => 'EST',
                'offset' => 2,
                'isdst' => false,
                'text' => '(UTC+02:00) Cairo',
                'utc' =>
                    [
                        'Africa/Cairo',
                    ],
            ],
            [
                'value' => 'Syria Standard Time',
                'abbr' => 'SDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) Damascus',
                'utc' =>
                    [
                        'Asia/Damascus',
                    ],
            ],
            [
                'value' => 'E. Europe Standard Time',
                'abbr' => 'EEDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) E. Europe',
                'utc' =>
                    [
                        'Asia/Nicosia',
                        'Europe/Athens',
                        'Europe/Bucharest',
                        'Europe/Chisinau',
                        'Europe/Helsinki',
                        'Europe/Kiev',
                        'Europe/Mariehamn',
                        'Europe/Nicosia',
                        'Europe/Riga',
                        'Europe/Sofia',
                        'Europe/Tallinn',
                        'Europe/Uzhgorod',
                        'Europe/Vilnius',
                        'Europe/Zaporozhye',
                    ],
            ],
            [
                'value' => 'South Africa Standard Time',
                'abbr' => 'SAST',
                'offset' => 2,
                'isdst' => false,
                'text' => '(UTC+02:00) Harare, Pretoria',
                'utc' =>
                    [
                        'Africa/Blantyre',
                        'Africa/Bujumbura',
                        'Africa/Gaborone',
                        'Africa/Harare',
                        'Africa/Johannesburg',
                        'Africa/Kigali',
                        'Africa/Lubumbashi',
                        'Africa/Lusaka',
                        'Africa/Maputo',
                        'Africa/Maseru',
                        'Africa/Mbabane',
                        'Etc/GMT-2',
                    ],
            ],
            [
                'value' => 'FLE Standard Time',
                'abbr' => 'FDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius',
                'utc' =>
                    [
                        'Europe/Kiev',
                        'Europe/Helsinki',
                        'Europe/Mariehamn',
                        'Europe/Riga',
                        'Europe/Sofia',
                        'Europe/Tallinn',
                        'Europe/Uzhgorod',
                        'Europe/Vilnius',
                        'Europe/Zaporozhye',
                    ],
            ],
            [
                'value' => 'Turkey Standard Time',
                'abbr' => 'TDT',
                'offset' => 3,
                'isdst' => false,
                'text' => '(UTC+03:00) Istanbul',
                'utc' =>
                    [
                        'Europe/Istanbul',
                    ],
            ],
            [
                'value' => 'Israel Standard Time',
                'abbr' => 'JDT',
                'offset' => 3,
                'isdst' => true,
                'text' => '(UTC+02:00) Jerusalem',
                'utc' =>
                    [
                        'Asia/Jerusalem',
                    ],
            ],
            [
                'value' => 'Arabic Standard Time',
                'abbr' => 'ARST',
                'offset' => 3,
                'isdst' => false,
                'text' => '(UTC+03:00) Baghdad, Kuwait, Riyadh',
                'utc' =>
                    [
                        'Asia/Baghdad',
                    ],
            ],
            [
                'value' => 'Kaliningrad Standard Time',
                'abbr' => 'KST',
                'offset' => 3,
                'isdst' => false,
                'text' => '(UTC+02:00) Kaliningrad',
                'utc' =>
                    [
                        'Europe/Kaliningrad',
                    ],
            ],
            [
                'value' => 'E. Africa Standard Time',
                'abbr' => 'EAFST',
                'offset' => 3,
                'isdst' => false,
                'text' => '(UTC+03:00) Nairobi',
                'utc' =>
                    [
                        'Africa/Addis_Ababa',
                        'Africa/Asmera',
                        'Africa/Dar_es_Salaam',
                        'Africa/Djibouti',
                        'Africa/Juba',
                        'Africa/Kampala',
                        'Africa/Khartoum',
                        'Africa/Mogadishu',
                        'Africa/Nairobi',
                        'Antarctica/Syowa',
                        'Etc/GMT-3',
                        'Indian/Antananarivo',
                        'Indian/Comoro',
                        'Indian/Mayotte',
                    ],
            ],
            [
                'value' => 'Moscow Standard Time',
                'abbr' => 'MSK',
                'offset' => 3,
                'isdst' => false,
                'text' => '(UTC+03:00) Moscow, St. Petersburg, Volgograd, Minsk',
                'utc' =>
                    [
                        'Europe/Moscow',
                        'Europe/Kirov',
                        'Europe/Simferopol',
                        'Europe/Volgograd',
                        'Europe/Minsk',
                    ],
            ],
            [
                'value' => 'Samara Time',
                'abbr' => 'SAMT',
                'offset' => 4,
                'isdst' => false,
                'text' => '(UTC+04:00) Samara, Ulyanovsk, Saratov',
                'utc' =>
                    [
                        'Europe/Astrakhan',
                        'Europe/Samara',
                        'Europe/Ulyanovsk',
                    ],
            ],
            [
                'value' => 'Iran Standard Time',
                'abbr' => 'IDT',
                'offset' => 4.5,
                'isdst' => true,
                'text' => '(UTC+03:30) Tehran',
                'utc' =>
                    [
                        'Asia/Tehran',
                    ],
            ],
            [
                'value' => 'Arabian Standard Time',
                'abbr' => 'ARAST',
                'offset' => 4,
                'isdst' => false,
                'text' => '(UTC+04:00) Abu Dhabi, Muscat',
                'utc' =>
                    [
                        'Asia/Dubai',
                        'Asia/Muscat',
                        'Etc/GMT-4',
                    ],
            ],
            [
                'value' => 'Azerbaijan Standard Time',
                'abbr' => 'AZDT',
                'offset' => 5,
                'isdst' => true,
                'text' => '(UTC+04:00) Baku',
                'utc' =>
                    [
                        'Asia/Baku',
                    ],
            ],
            [
                'value' => 'Georgian Standard Time',
                'abbr' => 'GET',
                'offset' => 4,
                'isdst' => false,
                'text' => '(UTC+04:00) Tbilisi',
                'utc' =>
                    [
                        'Asia/Tbilisi',
                    ],
            ],
            [
                'value' => 'Caucasus Standard Time',
                'abbr' => 'CST',
                'offset' => 4,
                'isdst' => false,
                'text' => '(UTC+04:00) Yerevan',
                'utc' =>
                    [
                        'Asia/Yerevan',
                    ],
            ],
            [
                'value' => 'Afghanistan Standard Time',
                'abbr' => 'AST',
                'offset' => 4.5,
                'isdst' => false,
                'text' => '(UTC+04:30) Kabul',
                'utc' =>
                    [
                        'Asia/Kabul',
                    ],
            ],
            [
                'value' => 'West Asia Standard Time',
                'abbr' => 'WAST',
                'offset' => 5,
                'isdst' => false,
                'text' => '(UTC+05:00) Ashgabat, Tashkent',
                'utc' =>
                    [
                        'Antarctica/Mawson',
                        'Asia/Aqtau',
                        'Asia/Aqtobe',
                        'Asia/Ashgabat',
                        'Asia/Dushanbe',
                        'Asia/Oral',
                        'Asia/Samarkand',
                        'Asia/Tashkent',
                        'Etc/GMT-5',
                        'Indian/Kerguelen',
                        'Indian/Maldives',
                    ],
            ],
            [
                'value' => 'Yekaterinburg Time',
                'abbr' => 'YEKT',
                'offset' => 5,
                'isdst' => false,
                'text' => '(UTC+05:00) Yekaterinburg',
                'utc' =>
                    [
                        'Asia/Yekaterinburg',
                    ],
            ],
            [
                'value' => 'Pakistan Standard Time',
                'abbr' => 'PKT',
                'offset' => 5,
                'isdst' => false,
                'text' => '(UTC+05:00) Islamabad, Karachi',
                'utc' =>
                    [
                        'Asia/Karachi',
                    ],
            ],
            [
                'value' => 'India Standard Time',
                'abbr' => 'IST',
                'offset' => 5.5,
                'isdst' => false,
                'text' => '(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi',
                'utc' =>
                    [
                        'Asia/Kolkata',
                    ],
            ],
            [
                'value' => 'Nepal Standard Time',
                'abbr' => 'NST',
                'offset' => 5.75,
                'isdst' => false,
                'text' => '(UTC+05:45) Kathmandu',
                'utc' =>
                    [
                        'Asia/Kathmandu',
                    ],
            ],
            [
                'value' => 'Central Asia Standard Time',
                'abbr' => 'CAAST',
                'offset' => 6,
                'isdst' => false,
                'text' => '(UTC+06:00) Nur-Sultan (Astana)',
                'utc' =>
                    [
                        'Antarctica/Vostok',
                        'Asia/Almaty',
                        'Asia/Bishkek',
                        'Asia/Qyzylorda',
                        'Asia/Urumqi',
                        'Etc/GMT-6',
                        'Indian/Chagos',
                    ],
            ],
            [
                'value' => 'Bangladesh Standard Time',
                'abbr' => 'BAST',
                'offset' => 6,
                'isdst' => false,
                'text' => '(UTC+06:00) Dhaka',
                'utc' =>
                    [
                        'Asia/Dhaka',
                        'Asia/Thimphu',
                    ],
            ],
            [
                'value' => 'SE Asia Standard Time',
                'abbr' => 'SEAST',
                'offset' => 7,
                'isdst' => false,
                'text' => '(UTC+07:00) Bangkok, Hanoi, Jakarta',
                'utc' =>
                    [
                        'Antarctica/Davis',
                        'Asia/Bangkok',
                        'Asia/Hovd',
                        'Asia/Jakarta',
                        'Asia/Phnom_Penh',
                        'Asia/Pontianak',
                        'Asia/Saigon',
                        'Asia/Vientiane',
                        'Etc/GMT-7',
                        'Indian/Christmas',
                    ],
            ],
            [
                'value' => 'N. Central Asia Standard Time',
                'abbr' => 'NCAST',
                'offset' => 7,
                'isdst' => false,
                'text' => '(UTC+07:00) Novosibirsk',
                'utc' =>
                    [
                        'Asia/Novokuznetsk',
                        'Asia/Novosibirsk',
                        'Asia/Omsk',
                    ],
            ],
            [
                'value' => 'China Standard Time',
                'abbr' => 'CHST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
                'utc' =>
                    [
                        'Asia/Hong_Kong',
                        'Asia/Macau',
                        'Asia/Shanghai',
                    ],
            ],
            [
                'value' => 'North Asia Standard Time',
                'abbr' => 'NAST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Krasnoyarsk',
                'utc' =>
                    [
                        'Asia/Krasnoyarsk',
                    ],
            ],
            [
                'value' => 'Singapore Standard Time',
                'abbr' => 'MPST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Kuala Lumpur, Singapore',
                'utc' =>
                    [
                        'Asia/Brunei',
                        'Asia/Kuala_Lumpur',
                        'Asia/Kuching',
                        'Asia/Makassar',
                        'Asia/Manila',
                        'Asia/Singapore',
                        'Etc/GMT-8',
                    ],
            ],
            [
                'value' => 'W. Australia Standard Time',
                'abbr' => 'WAUST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Perth',
                'utc' =>
                    [
                        'Antarctica/Casey',
                        'Australia/Perth',
                    ],
            ],
            [
                'value' => 'Taipei Standard Time',
                'abbr' => 'TST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Taipei',
                'utc' =>
                    [
                        'Asia/Taipei',
                    ],
            ],
            [
                'value' => 'Ulaanbaatar Standard Time',
                'abbr' => 'UST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Ulaanbaatar',
                'utc' =>
                    [
                        'Asia/Choibalsan',
                        'Asia/Ulaanbaatar',
                    ],
            ],
            [
                'value' => 'North Asia East Standard Time',
                'abbr' => 'NAEST',
                'offset' => 8,
                'isdst' => false,
                'text' => '(UTC+08:00) Irkutsk',
                'utc' =>
                    [
                        'Asia/Irkutsk',
                    ],
            ],
            [
                'value' => 'Japan Standard Time',
                'abbr' => 'JST',
                'offset' => 9,
                'isdst' => false,
                'text' => '(UTC+09:00) Osaka, Sapporo, Tokyo',
                'utc' =>
                    [
                        'Asia/Dili',
                        'Asia/Jayapura',
                        'Asia/Tokyo',
                        'Etc/GMT-9',
                        'Pacific/Palau',
                    ],
            ],
            [
                'value' => 'Korea Standard Time',
                'abbr' => 'SKST',
                'offset' => 9,
                'isdst' => false,
                'text' => '(UTC+09:00) Seoul',
                'utc' =>
                    [
                        'Asia/Pyongyang',
                        'Asia/Seoul',
                    ],
            ],
            [
                'value' => 'Cen. Australia Standard Time',
                'abbr' => 'CNAST',
                'offset' => 9.5,
                'isdst' => false,
                'text' => '(UTC+09:30) Adelaide',
                'utc' =>
                    [
                        'Australia/Adelaide',
                        'Australia/Broken_Hill',
                    ],
            ],
            [
                'value' => 'AUS Central Standard Time',
                'abbr' => 'ACST',
                'offset' => 9.5,
                'isdst' => false,
                'text' => '(UTC+09:30) Darwin',
                'utc' =>
                    [
                        'Australia/Darwin',
                    ],
            ],
            [
                'value' => 'E. Australia Standard Time',
                'abbr' => 'EAST',
                'offset' => 10,
                'isdst' => false,
                'text' => '(UTC+10:00) Brisbane',
                'utc' =>
                    [
                        'Australia/Brisbane',
                        'Australia/Lindeman',
                    ],
            ],
            [
                'value' => 'AUS Eastern Standard Time',
                'abbr' => 'AEST',
                'offset' => 10,
                'isdst' => false,
                'text' => '(UTC+10:00) Canberra, Melbourne, Sydney',
                'utc' =>
                    [
                        'Australia/Melbourne',
                        'Australia/Sydney',
                    ],
            ],
            [
                'value' => 'West Pacific Standard Time',
                'abbr' => 'WPST',
                'offset' => 10,
                'isdst' => false,
                'text' => '(UTC+10:00) Guam, Port Moresby',
                'utc' =>
                    [
                        'Antarctica/DumontDUrville',
                        'Etc/GMT-10',
                        'Pacific/Guam',
                        'Pacific/Port_Moresby',
                        'Pacific/Saipan',
                        'Pacific/Truk',
                    ],
            ],
            [
                'value' => 'Tasmania Standard Time',
                'abbr' => 'TST',
                'offset' => 10,
                'isdst' => false,
                'text' => '(UTC+10:00) Hobart',
                'utc' =>
                    [
                        'Australia/Currie',
                        'Australia/Hobart',
                    ],
            ],
            [
                'value' => 'Yakutsk Standard Time',
                'abbr' => 'YST',
                'offset' => 9,
                'isdst' => false,
                'text' => '(UTC+09:00) Yakutsk',
                'utc' =>
                    [
                        'Asia/Chita',
                        'Asia/Khandyga',
                        'Asia/Yakutsk',
                    ],
            ],
            [
                'value' => 'Central Pacific Standard Time',
                'abbr' => 'CPST',
                'offset' => 11,
                'isdst' => false,
                'text' => '(UTC+11:00) Solomon Is., New Caledonia',
                'utc' =>
                    [
                        'Antarctica/Macquarie',
                        'Etc/GMT-11',
                        'Pacific/Efate',
                        'Pacific/Guadalcanal',
                        'Pacific/Kosrae',
                        'Pacific/Noumea',
                        'Pacific/Ponape',
                    ],
            ],
            [
                'value' => 'Vladivostok Standard Time',
                'abbr' => 'VST',
                'offset' => 11,
                'isdst' => false,
                'text' => '(UTC+11:00) Vladivostok',
                'utc' =>
                    [
                        'Asia/Sakhalin',
                        'Asia/Ust-Nera',
                        'Asia/Vladivostok',
                    ],
            ],
            [
                'value' => 'New Zealand Standard Time',
                'abbr' => 'NZST',
                'offset' => 12,
                'isdst' => false,
                'text' => '(UTC+12:00) Auckland, Wellington',
                'utc' =>
                    [
                        'Antarctica/McMurdo',
                        'Pacific/Auckland',
                    ],
            ],
            [
                'value' => 'Fiji Standard Time',
                'abbr' => 'FST',
                'offset' => 12,
                'isdst' => false,
                'text' => '(UTC+12:00) Fiji',
                'utc' =>
                    [
                        'Pacific/Fiji',
                    ],
            ],
            [
                'value' => 'Magadan Standard Time',
                'abbr' => 'MST',
                'offset' => 12,
                'isdst' => false,
                'text' => '(UTC+12:00) Magadan',
                'utc' =>
                    [
                        'Asia/Anadyr',
                        'Asia/Kamchatka',
                        'Asia/Magadan',
                        'Asia/Srednekolymsk',
                    ],
            ],
            [
                'value' => 'Samoa Standard Time',
                'abbr' => 'SST',
                'offset' => 13,
                'isdst' => false,
                'text' => '(UTC+13:00) Samoa',
                'utc' =>
                    [
                        'Pacific/Apia',
                    ],
            ],
        ];
    }
}
