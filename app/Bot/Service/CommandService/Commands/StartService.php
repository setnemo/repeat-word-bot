<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\Messages\StartMessage;
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
                    'text' => StartMessage::WELCOME_TEXT,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'reply_markup' => $keyboard,
                    'disable_notification' => 1,
                ]
            )
        );

        return $this;
    }
}
