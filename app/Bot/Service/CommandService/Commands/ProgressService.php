<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\Messages\ProgressMessage;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

/**
 * Class ProgressService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class ProgressService extends BaseDefaultCommandService
{
    protected TrainingRepository $trainingRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->trainingRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Training::class);
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function execute(): CommandInterface
    {
        $userId  = $this->getOptions()->getChatId();
        $records = $this->trainingRepository->getMyStats($userId);
        $text    = '';
        $text    = BotHelper::getProgressText($records, $text);

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id'                  => $userId,
                    'text'                     => '' === trim($text) ? ProgressMessage::EMPTY_VOCABULARY : ProgressMessage::HINT . $text,
                    'parse_mode'               => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification'     => 1,
                ]
            )
        );

        return $this;
    }
}
