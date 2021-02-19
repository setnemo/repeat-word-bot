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
            ['Мой прогресс', 'Коллекции слов', 'Тренировка',],
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
            ['Назад', 'To English', 'From English'],
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
            'Тренировка' => 'StartTraining',
            'Назад' => 'Start'
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
}
