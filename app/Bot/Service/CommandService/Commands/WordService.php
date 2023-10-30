<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RepeatBot\Common\Config;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Database;
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
protected WordRepository $wordRepository;
protected Config $config;

    /**
     * {@inheritDoc}
     */
public function __construct(CommandOptions $options)
{
    /** @psalm-suppress PropertyTypeCoercion */
    $this->wordRepository = Database::getInstance()->getEntityManager()->getRepository(Word::class);
    /** @psalm-suppress PropertyTypeCoercion */
    $this->config = App::getInstance()->getConfig();
    $this->cache  = Cache::getInstance()->init($this->config);
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
    $array   = $this->getOptions()->getPayload();
    $command = $array[self::CMD];
    $text    = match ($command) {
        static::UPDATE => $this->update(params: (string)$array[self::BODY] ?? ''),
    default => $this->show(id: intval($array[self::BODY]) ?? 0),
    };
        $this->setResponse(
            new ResponseDirector('sendMessage', [
            'chat_id'              => $this->getOptions()->getChatId(),
            'text'                 => $text,
            'parse_mode'           => 'markdown',
            'disable_notification' => 1,
            ])
        );

    return $this;
        }

    protected function show(int $id): string
    {
        $item = $this->wordRepository->findOneBy(['id' => $id]);

        return $item ? strtr("`:word`:\n\n`:translate`", [':word' => $item->getWord(), ':translate' => $item->getTranslate()]) : 'Слово не знайдено!';
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
        $item = $this->wordRepository->findOneBy(['id' => $first]);
        if (!$item) {
            return 'Помилка оновлення';
        }
        $this->wordRepository->updateWord($first, $newTranslate);

        return strtr(
            "Слово :word[:id]:\n\nOld:\n:old\n\nNew:\n:new` оновлено!",
            [
            ':word' => $item->getWord(),
            ':old'  => $item->getTranslate(),
            ':id'   => $first,
            ':new'  => $newTranslate,
            ]
        );
    }
}
