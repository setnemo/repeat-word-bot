<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use RepeatBot\Bot\Service\CommandService\ResponseDirector;

class EmptyCallbackService extends BaseCommandService
{
    public function execute(): CommandInterface
    {
        $this->setResponse(
            new ResponseDirector(
                'answerCallbackQuery',
                [
                    'callback_query_id' => $this->getOptions()->getCallbackQueryId(),
                    'text'              => '',
                    'show_alert'        => true,
                    'cache_time'        => 3,
                ]
            )
        );

        return $this;
    }
}
