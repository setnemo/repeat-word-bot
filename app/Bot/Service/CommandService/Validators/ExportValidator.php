<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Core\Database\Database;
use RepeatBot\Core\ORM\Entities\Export;
use RepeatBot\Core\ORM\Repositories\ExportRepository;

class ExportValidator implements ValidateCommand
{
    private ExportRepository $exportRepository;

    public function __construct()
    {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->exportRepository = Database::getInstance()
            ->getEntityManager()
            ->getRepository(Export::class);
    }

    public function validate(CommandOptions $options): array
    {
        if ($this->exportRepository->userHaveExport($options->getChatId())) {
            $data = [
                'chat_id' => $options->getChatId(),
                'text' => $this->getHaveExportErrorText(),
                'parse_mode' => 'markdown',
                'disable_notification' => 1,
            ];
            return [
                new ResponseDirector('sendMessage', $data)
            ];
        }

        $payload = $options->getPayload();
        if (
            count($payload) === 2 &&
            (
                !in_array($payload[0], ['FromEnglish','ToEnglish']) ||
                !in_array($payload[1], ['first','second','third','fourth','fifth','sixth','never'])
            ) ||
            count($payload) > 2
        ) {
            $data = [
                'chat_id' => $options->getChatId(),
                'text' => $this->getInvalidPayloadErrorText(),
                'parse_mode' => 'markdown',
                'disable_web_page_preview' => true,
                'disable_notification' => 1,
            ];
            return [
                new ResponseDirector('sendMessage', $data)
            ];
        }

        return [];
    }

    private function getHaveExportErrorText(): string
    {
        return 'У вас есть экспорт слов, дождитесь очереди для создания файла';
    }

    private function getInvalidPayloadErrorText(): string
    {
        return "Допустимые форматы команды\n - /export\n - /export FromEnglish first\n" .
            " - /export ToEnglish second\n\n Где первое слово режим без пробела, а второе название итерации. " .
            "Посмотреть сколько у вас слов в какой итерации можно командой /progress";
    }
}
