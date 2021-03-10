<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Exception;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Training;
use RepeatBot\Core\ORM\Repositories\TrainingRepository;

/**
 * Class ProgressService
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class ProgressService extends BaseCommandService
{
    private TrainingRepository $trainingRepository;
    
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
     * @throws Exception
     */
    public function execute(): CommandInterface
    {
        $userId = $this->getOptions()->getChatId();
        $records = $this->trainingRepository->getMyStats($userId);
        $text = '';
        $flag = false;
        foreach ($records as $type => $items) {
            foreach ($items as $item) {
                $flag = true;
                $status = ucfirst($item['status']);
                $text .= BotHelper::getAnswer(
                    "\[{$type}] {$status} итерация: ",
                    (int)$item['counter']
                ) . "\n";
            }
        }

        $this->setResponse(
            new ResponseDirector(
                'sendMessage',
                [
                    'chat_id' => $userId,
                    'text' => $flag ?  $this->getText() . $text : 'Ваш словарь пуст. Пожалуйста добавьте коллекцию!',
                    'parse_mode' => 'markdown',
                    'disable_web_page_preview' => true,
                    'disable_notification' => 1,
                ]
            )
        );

        return $this;
    }
    
    /**
     * @return string
     */
    private function getText(): string
    {
        return "`Подсказка:`\nFirst итерация: повтор слова через 24 часа\n" .
            "Second итерация: повтор слова через 3 дня\n" .
            "Third итерация: повтор слова через 7 дней\n" .
            "Fourth итерация: повтор слова через 1 месяц\n" .
            "Fifth итерация: повтор слова через 3 месяца\n" .
            "Sixth итерация: повтор слова через 6 месяца\n" .
            "Never итерация: повтор слова через 1 год\n\n" .
            "`Сброс прогресса:`\nИспользуйте команду `/reset my progress`\n" .
            "Будьте осторожны, сброс не обратим и вам придется начать итерации с начала\n\n" .
            "`Ваша статистика:\n`";
    }
}
