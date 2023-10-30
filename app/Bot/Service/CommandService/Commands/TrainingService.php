<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\Messages\TrainingMessage;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class TrainingService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class TrainingService extends BaseDefaultCommandService
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function execute(): CommandInterface
    {
        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getTrainingKeyboard());
        $keyboard->setResizeKeyboard(true);

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $this->getOptions()->getChatId(),
                    'text'                     => TrainingMessage::CHOOSE_TEXT,
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup'             => $keyboard,
                    'disable_notification'     => 1,
                ]
            )
        );

        return $this;
    }
}
