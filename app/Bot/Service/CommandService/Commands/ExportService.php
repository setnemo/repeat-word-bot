<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
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
     */
    public function execute(): CommandInterface
    {
        $array = $this->getOptions()->getPayload();

        if (count($array) === 1 && $array[0] === '') {
            $this->exportRepository->create($this->getOptions()->getChatId(), $this->getOptions()->getChatId(), 'FromEnglish');
        } elseif (
            count($array) == 2 &&
            in_array($array[0], ['FromEnglish','ToEnglish']) &&
            in_array($array[1], ['first','second','third','fourth','fifth','sixth','never'])
        ) {
            $this->exportRepository->create($this->getOptions()->getChatId(), $this->getOptions()->getChatId(), $array[0] . '_' . $array[1]);
        }

        $this->setResponse(new ResponseDirector('sendMessage', [
            'chat_id' => $this->getOptions()->getChatId(),
            'text' => "Создание экспорта поставлено в очередь. Как только файл будет готов вы получите его в личном сообщении.",
            'parse_mode' => 'markdown',
            'disable_web_page_preview' => true,
            'disable_notification' => 1,
        ]));
        return $this;
    }
}
