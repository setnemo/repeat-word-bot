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
            ['Коллекции слов', 'To English', ],
            ['Назад', 'From English'],
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
        $text .= "Не добавляйте сразу слишком много, сначала отправьте на долгие итерации небольшие коллекции.\n";
        $text .= "При добавлении Коллекции слова добавляются в оба типа тренировок (From English + To English)\n";
        $text .= "Также есть команда /reset для сброса, если вы по ошибке добавили слишком много или хотите начать сначала\n\n";
        $text .= "Каждая коллекция уникальна! Слова НЕ ПОВТОРЯЮТСЯ. Вас ждет приключение на 17814 слов! ";
        $text .= "Рекомендую пройти маленькие коллекции, а потом браться за большие.\n\n";
        $text .= "При добавлении Мега Коллекций не спешите, дождитесь ответа сервера, это 'тяжелая' операция Удачи!\n";

        return $text;
    }
}
