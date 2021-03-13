<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

/**
 * Class CallbackQueryDirectorFabric
 * @package RepeatBot\Bot\Service\CommandService\Commands
 */
class CallbackQueryDirectorFabric
{
    /**
     * DirectorFabric constructor.
     *
     * @param CommandOptions $options
     */
    public function __construct(protected CommandOptions $options)
    {
    }

    /**
     * @return CommandDirector
     */
    public function getCommandDirector(): CommandDirector
    {
        $command = $this->getOptions()->getPayload()[0] ?? '';

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
        $command = $this->getOptions()->getPayload()[1] ?? '';

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
                command: $command,
                payload: $this->getOptions()->getPayload(),
                chatId: $this->getOptions()->getChatId(),
                messageId: $this->getOptions()->getMessageId(),
                callbackQueryId: $this->getOptions()->getCallbackQueryId()
            )
        );
    }

    /**
     * @return CommandOptions
     */
    public function getOptions(): CommandOptions
    {
        return $this->options;
    }
}
