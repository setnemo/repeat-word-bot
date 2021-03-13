<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

/**
 * Class StartService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class StartService extends BaseDefaultCommandService
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $this->getOptions()->getChatId(),
                    'text' => $this->getText(),
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
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
        return "Привет! Я помогаю учить английские слова по методике интервального повторения.\n\n" .
            "Интервальные повторения (англ. spaced repetition) — техника удержания в памяти, " .
            "заключающаяся в повторении запомненного учебного материала по определённым, " .
            "постоянно возрастающим интервалам.\n\nКак только вы правильно отвечаете, " .
            'слово переходит на следующую итерацию для повторения. Всего итераций 7. ' .
            'Первая через сутки, вторая через три дня, потом через семь, потом через месяц, три, полгода и год.' .
            "\n\nТакже пользуйтесь кнопкой 'Я не знаю'. Она поможет Вам разобраться с новыми для Вас словами. " .
            ' А также не добавляйте сразу слишком много, сначала отправьте на долгие итерации небольшие коллекции. Удачи!' .
            "\n\nP.S. Если вдруг слово не воспроизводится - отправляйте это сообщение форвардом @omentes в личку.";
    }
}
