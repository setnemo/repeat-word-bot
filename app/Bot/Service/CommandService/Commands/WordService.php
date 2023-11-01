<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Monolog\Logger;
use RepeatBot\Common\Config;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database;
use RepeatBot\Core\Log;
use RepeatBot\Core\Metric;
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
    public const SHOW_WORD_TEMPLATE = "`:word`(:id):\n`:translate`\n\nДля копії команди оновлення тисніть на:\n`/word update :id :translate`";
    public const UPDATED_WORD_TEMPLATE = "Слово `:word`(`:id`) оновлено перекладом: `:new`\n\nЩоб повернути попередній переклад:\n`/word update :id :old`";
    protected WordRepository $wordRepository;
    protected Config $config;
    protected Logger $logger;

    /**
     * @inheritDoc
     */
    public function __construct(CommandOptions $options)
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->wordRepository = Database::getInstance()->getEntityManager()->getRepository(Word::class);
        /** @psalm-suppress PropertyTypeCoercion */
        $this->config = App::getInstance()->getConfig();
        $this->cache  = Cache::getInstance()->init($this->config);
        $this->logger = Log::getInstance()->getAdminLogger($this->config);
        Metric::getInstance()->init($this->config)->increaseMetric('update_words');
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
        $options = $this->getOptions();
        $this->logger->critical('request', [
            'payload' => $options->getPayload(),
            'chat_id' => $options->getChatId(),
        ]);
        $array   = $options->getPayload();
        $command = $array[self::CMD];
        $body    = $array[self::BODY];
        $text    = match ($command) {
            static::UPDATE => $this->update(params: (string)$body ?? ''),
            default => is_numeric($body) ? $this->showById(id: intval($body) ?? 0) : $this->showByWord($body),
        };
        $this->setResponse(
            new ResponseDirector('sendMessage', [
                'chat_id'              => $options->getChatId(),
                'text'                 => $text,
                'parse_mode'           => 'markdown',
                'disable_notification' => 1,
            ])
        );

        return $this;
    }

    /**
     * @param int $id
     * @return string
     */
    protected function showById(int $id): string
    {
        $item = $this->wordRepository->findOneBy(['id' => $id]);

        return $item ? strtr(
            self::SHOW_WORD_TEMPLATE,
            [':id' => $item->getId(), ':word' => $item->getWord(), ':translate' => $item->getTranslate()]
        ) : 'Слово не знайдено!';
    }

    /**
     * @param string $word
     * @return string
     */
    protected function showByWord(string $word): string
    {
        $item = $this->wordRepository->findOneBy(['word' => $word]);

        return $item ? strtr(
            self::SHOW_WORD_TEMPLATE,
            [':id' => $item->getId(), ':word' => $item->getWord(), ':translate' => $item->getTranslate()]
        ) : 'Слово не знайдено!';
    }

    /**
     * @param string $params
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function update(string $params): string
    {
        $explode = explode(' ', $params);
        $first   = (int)array_shift($explode);
        $explode = explode('; ', trim(implode(' ', $explode)));
        if (empty($explode) || 1 > $first || 18869 < $first) {
            return 'Помилка оновлення';
        }
        $newTranslate = implode('; ', $explode);
        if (empty($newTranslate)) {
            return 'Помилка оновлення';
        }
        $item         = $this->wordRepository->findOneBy(['id' => $first]);
        $translateOld = $item->getTranslate();
        if (!$item) {
            return 'Помилка оновлення';
        }
        $this->wordRepository->updateWord($first, $newTranslate);

        return strtr(
            self::UPDATED_WORD_TEMPLATE,
            [
                ':word' => $item->getWord(),
                ':old'  => $translateOld,
                ':id'   => $first,
                ':new'  => $newTranslate,
            ]
        );
    }
}
