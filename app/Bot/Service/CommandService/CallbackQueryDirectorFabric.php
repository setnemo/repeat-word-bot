<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

/**
 * Class CallbackQueryDirectorFabric
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class CallbackQueryDirectorFabric
{
    private string $query;
    private int $chatId;
    private int $messageId;
    private int $callbackQueryId;
    private array $payload;

    /**
     * DirectorFabric constructor.
     *
     * @param string $query
     * @param int    $chatId
     * @param int    $messageId
     * @param int    $callbackQueryId
     */
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

    /**
     * @return CommandDirector
     */
    public function getCommandDirector(): CommandDirector
    {
        $command = $this->payload[0];

        return match($command) {
            'collections' => $this->makeCollectionCommand(),
            'settings'    => $this->makeSettingsCommand(),
        default           => $this->makeEmptyCallback(),
        };
    }

    /**
     * @return CommandDirector
     */
    private function makeSettingsCommand(): CommandDirector
    {
        $command = $this->payload[1];

        return match($command) {
            'voices'   => $this->makeSettingsVoicesCommand(),
            'silent'   => $this->makeSettingsSilentCommand(),
            'priority' => $this->makeSettingsPriorityCommand(),
        default        => $this->makeEmptyCallback(),
        };
    }

    /**
     * @return CommandDirector
     */
    private function makeCollectionCommand(): CommandDirector
    {
        return $this->makeDirector('collections');
    }

    /**
     * @return CommandDirector
     */
    private function makeEmptyCallback(): CommandDirector
    {
        return $this->makeDirector('empty');
    }

    /**
     * @return CommandDirector
     */
    private function makeSettingsVoicesCommand(): CommandDirector
    {
        return $this->makeDirector('settings_voices');
    }

    /**
     * @return CommandDirector
     */
    private function makeSettingsSilentCommand(): CommandDirector
    {
        return $this->makeDirector('settings_silent');
    }

    /**
     * @return CommandDirector
     */
    private function makeSettingsPriorityCommand(): CommandDirector
    {
        return $this->makeDirector('settings_priority');
    }

    /**
     * @param string $command
     *
     * @return CommandDirector
     */
    private function makeDirector(string $command): CommandDirector
    {
        return new CommandDirector(
            new CommandOptions(
                $command,
                '',
                explode('_', $this->query),
                $this->chatId,
                $this->messageId,
                $this->callbackQueryId
            )
        );
    }
}
