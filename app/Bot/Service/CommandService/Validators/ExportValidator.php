<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Messages\ExportMessage;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Repositories\ExportRepository;

/**
 * Class ExportValidator
 * @package RepeatBot\Bot\Service\CommandService\Validators
 */
class ExportValidator implements ValidateCommand
{
    private ExportRepository $exportRepository;

    /**
     * ExportValidator constructor.
     */
    public function __construct()
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->exportRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Export::class);
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function validate(CommandOptions $options): array
    {
        if ($this->exportRepository->userHaveExport($options->getChatId())) {
            return $this->createUserErrorResponse($options, ExportMessage::ERROR_HAVE_EXPORT_TEXT);
        }

        $payload = $options->getPayload();
        if (
            count($payload) === 2 &&
            (
                !in_array($payload[0], BotHelper::getTrainingTypes()) ||
                !in_array($payload[1], BotHelper::getTrainingStatuses())
            ) ||
            count($payload) > 2 ||
            count($payload) === 1 && $payload[0] !== ''
        ) {
            return $this->createUserErrorResponse($options, ExportMessage::ERROR_INVALID_PAYLOAD_TEXT);
        }

        return [];
    }

    /**
     * @param CommandOptions $options
     * @param string         $text
     *
     * @return ResponseDirector[]
     * @throws \Exception
     */
    private function createUserErrorResponse(CommandOptions $options, string $text): array
    {
        $data = [
            'chat_id' => $options->getChatId(),
            'text' => $text,
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
        ];

        return [
            new ResponseDirector('sendMessage', $data)
        ];
    }
}
