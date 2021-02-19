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
            ['My Vocabulary', 'Collections', 'Start Training',],
        ];
    }
    /**
     * @return array
     */
    public static function getInTrainingKeyboard(): array
    {
        return [
            ['Stop Training', 'Don\'t know this word'],
        ];
    }

    /**
     * @return array
     */
    public static function getTrainingKeyboard(): array
    {
        return [
            ['My Vocabulary', 'Voice To English', 'To English'],
            ['Main Menu', 'Voice From English', 'From English'],
        ];
    }

    /**
     * @return string[]
     */
    public static function getCommands(): array
    {
        return [
            'Collections' => 'Collections',
            'My Vocabulary' => 'MyVocabulary',
            'From English' => 'TextEnglish',
            'Voice From English' => 'VoiceEnglish',
            'VoiceFromEnglish' => 'VoiceEnglish',
            'FromEnglish' => 'TextEnglish',
            'To English' => 'TextEnglish',
            'ToEnglish' => 'TextEnglish',
            'Voice To English' => 'VoiceEnglish',
            'VoiceToEnglish' => 'VoiceEnglish',
            'Start Training' => 'StartTraining',
            'Main Menu' => 'Start'
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
                    'text' => "{$record->getName()} ({$record->getLanguage()})",
                    'callback_data' => "collection_{$record->getId()}"
                ];
            } else {
                $tmp[] = [
                    'text' => "{$record->getName()} ({$record->getLanguage()})",
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
            'VoiceFromEnglish',
            'VoiceToEnglish',
        ];
    }
}
