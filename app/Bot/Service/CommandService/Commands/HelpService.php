<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

/**
 * Class HelpService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class HelpService extends BaseCommandService
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $this->getOptions()->getChatId(),
                    'text' => $this->getText(),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );

        return $this;
    }

    /**
     * @return string
     */
    private function getText(): string
    {
        return "`Справка по использованию бота:`\n\n" .
            "вызвать справку во время тренировки:\n - /help\n\n" .
            "пропустить слово:\n - Нажать кнопку \[Я не знаю]\n - Написать русскую букву Х\n" .
            " - Написать английскую букву P\n - Написать точку\n - Написать 'не знаю' или 'dont know'\n\n" .
            "отправить слово в итерацию never (1 год до повторения):\n - Написать 1\n\n" .
            "удаление, сброс прогресса:\n - команда /del my progress для удаления всего прогресса\n" .
            " - команда /del collection <num> для удаления конкретной коллекции, где <num> это номер, например 1\n" .
            " - команда /reset my progress для сброса всего прогресса\n" .
            " - команда /reset collection <num> для сброса прогресса конкретной коллекции, где <num> это номер, например 1\n\n" .
            "посмотреть прогресс:\n - /progress\n - зайти в \[Мой прогресс] в меню тренировок(/training)\n\n" .
            "включить уведомления со звуком:\n - /settings\n - зайти в \[Настройки] в главном меню (/start)\n\n" .
            "персональные уведомления:\n - /alarm 9:00 - 9 утра по Киеву\n - /alarm 21:00 - 9 вечера по Киеву\n" .
            " - /alarm MSK 19:00 - 7 вечера по Москве\n - /alarm EDT 21:00 - 9 вечера по Нью-Йорку\n" .
            " - /alarm list - список персональных напоминаний\n - /alarm reset - удалить напоминания\n" .
            " - /time - список поддерживаемых кодов часовых поясов\n\n" .
            "экспорт слов в pdf:\n - /export - экспорт всех изучаемых слов\n" .
            " - /export ToEnglish first - экспорт всех слов с тренировки ToEnglish в итерации first " .
            "(например не получается нормально запомнить и хочется выписать их отдельно)\n\n";
    }
}
