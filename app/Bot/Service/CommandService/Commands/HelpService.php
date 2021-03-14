<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use RepeatBot\Bot\Service\CommandService\Messages\HelpMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

/**
 * Class HelpService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class HelpService extends BaseDefaultCommandService
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
                    'text' => HelpMessage::HELP_TEXT,
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );

        return $this;
    }
}
