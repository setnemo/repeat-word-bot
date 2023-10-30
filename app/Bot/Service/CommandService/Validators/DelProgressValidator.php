<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Validators;

use Longman\TelegramBot\Entities\Keyboard;
use RepeatBot\Bot\BotHelper;
use RepeatBot\Bot\Service\CommandService\Messages\DelMessage;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Exception\SupportTypeException;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Validator\ValidateCommand;

class DelProgressValidator implements ValidateCommand
{
    /**
     * {@inheritDoc}
     * @throws SupportTypeException
     */
    public function validate(CommandOptions $options): array
    {
        $payload = $options->getPayload();

        if (
            ['my', 'progress'] === $payload ||
            $payload[0] === 'collection' && intval($payload[1]) > 0 && intval($payload[1]) < 37
        ) {
            return [];
        }

        /** @psalm-suppress TooManyArguments */
        $keyboard = new Keyboard(...BotHelper::getDefaultKeyboard());
        $keyboard->setResizeKeyboard(true);
        $data = [
            'chat_id'                  => $options->getChatId(),
            'text'                     => $this->getErrorText(),
            'parse_mode'               => 'markdown',
            'disable_web_page_preview' => true,
            'reply_markup'             => $keyboard,
            'disable_notification'     => 1,
        ];
        return [
            new ResponseDirector('sendMessage', $data),
        ];
    }

    protected function getErrorText(): string
    {
        return DelMessage::ERROR_TEXT;
    }
}
