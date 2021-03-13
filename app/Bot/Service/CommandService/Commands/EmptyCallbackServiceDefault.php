<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;

/**
 * Class EmptyCallbackService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class EmptyCallbackServiceDefault extends BaseDefaultCommandService
{
    /**
     * {@inheritDoc}
     * @throws Exception
     */
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
