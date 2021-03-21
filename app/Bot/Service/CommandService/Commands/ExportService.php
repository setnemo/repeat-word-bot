<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\BotHelper;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use RepeatBot\Bot\Service\CommandService\Messages\ExportMessage;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Repositories\ExportRepository;

class ExportService extends BaseDefaultCommandService
{
    private ExportRepository $exportRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->exportRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Export::class);
        parent::__construct($options);
    }

    /**
     * {@inheritDoc}
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws SupportTypeException
     */
    public function execute(): CommandInterface
    {
        $array = $this->getOptions()->getPayload();

        if (count($array) === 1 && $array[0] === '') {
            $this->exportRepository->create($this->getOptions()->getChatId(), $this->getOptions()->getChatId(), 'FromEnglish');
        } elseif (
            count($array) == 2 &&
            in_array($array[0], BotHelper::getTrainingTypes()) &&
            in_array($array[1], BotHelper::getTrainingStatuses())
        ) {
            $this->exportRepository->create($this->getOptions()->getChatId(), $this->getOptions()->getChatId(), $array[0] . '_' . $array[1]);
        }
        $this->setResponse(new ResponseDirector('sendMessage', [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => ExportMessage::EXPORT_TEXT,
            'parse_mode' => 'markdown',
            'disable_notification' => 1,
        ]));

        return $this;
    }
}
