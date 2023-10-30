<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use RepeatBot\Bot\Service\CommandService\Messages\HelpMessage;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class HelpService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class HelpService extends BaseDefaultCommandService
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function execute(): CommandInterface
    {
        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $this->getOptions()->getChatId(),
                    'text'                     => HelpMessage::HELP_TEXT,
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification'     => 1,
                ]
            )
        );

        return $this;
    }
}
