<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

class DirectorFabric
{
    private string $query;
    private int $chatId;
    private int $messageId;
    private int $callbackQueryId;
    private array $payload;

    public function __construct(
        string $query = '',
        int $chatId = 0,
        int $messageId = 0,
        int $callbackQueryId = 0
    ) {
        $this->query = $query;
        $this->chatId = $chatId;
        $this->messageId = $messageId;
        $this->callbackQueryId = $callbackQueryId;

        $this->payload = explode('_', $this->query);
    }

    public function getCommandDirector(): CommandDirector
    {
        $command = $this->payload[0];

        return match($command) {
            'collections' => $this->makeCollectionCommand(),
            'settings'    => $this->makeSettingsCommand(),
        default       => $this->makeEmptyCallback(),
        };
    }

    private function makeSettingsCommand(): CommandDirector
    {
        $command = $this->payload[1];

        return match($command) {
            'voices'   => $this->makeSettingsVoicesCommand(),
            'silent'   => $this->makeSettingsSilentCommand(),
            'priority' => $this->makeSettingsPriorityCommand(),
        default    => $this->makeEmptyCallback(),
        };
    }

    private function makeCollectionCommand(): CommandDirector
    {
        return $this->makeDirector('collection');
    }

    private function makeEmptyCallback(): CommandDirector
    {
        return $this->makeDirector('empty');
    }

    private function makeSettingsVoicesCommand()
    {
        return $this->makeDirector('settings_voices');
    }

    private function makeSettingsSilentCommand()
    {
        return $this->makeDirector('settings_silent');
    }

    private function makeSettingsPriorityCommand()
    {
        return $this->makeDirector('settings_priority');
    }

    private function makeDirector(string $command): CommandDirector
    {
        return new CommandDirector(
            new CommandOptions(
                $command,
                explode('_', $this->query),
                $this->chatId,
                $this->messageId,
                $this->callbackQueryId
            )
        );
    }
}
