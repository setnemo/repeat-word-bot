<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Core\Database;
use RepeatBot\Core\ORM\Entities\Word;
use RepeatBot\Core\ORM\Repositories\WordRepository;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;

class WordService extends BaseDefaultCommandService
{
    public const CMD = 'cmd';
    public const BODY = 'body';
    public const UPDATE = 'update';
    public const SHOW = 'show';
    private WordRepository $wordRepository;

    /**
     * {@inheritDoc}
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->wordRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Word::class);
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
        $array   = $this->getOptions()->getPayload();
        $command = $array[self::CMD];
        $body    = $array[self::BODY];
        $this->setResponse(
            new ResponseDirector('sendMessage', [
                'chat_id'              => $this->getOptions()->getChatId(),
                'text'                 => '',
                'parse_mode'           => 'markdown',
                'disable_notification' => 1,
            ])
        );

        return $this;
    }
}
