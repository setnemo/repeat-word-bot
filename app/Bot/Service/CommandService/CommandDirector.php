<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Bot\Service\CommandService\Commands\CollectionService;
use RepeatBot\Bot\Service\CommandService\Commands\CommandInterface;
use RepeatBot\Bot\Service\CommandService\Commands\DelService;
use RepeatBot\Bot\Service\CommandService\Commands\EmptyCallbackService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsPriorityService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsSilentService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsVoicesService;
use RepeatBot\Bot\Service\CommandService\Validators\AlarmValidator;
use RepeatBot\Bot\Service\CommandService\Validators\DelValidator;

class CommandDirector
{
    public function __construct(protected CommandOptions $options)
    {
    }

    public function getOptions(): CommandOptions
    {
        return $this->options;
    }

    public function makeService(): CommandInterface
    {
        return match($this->getOptions()->getCommand()) {
            'alarm' => $this->makeAlarmCommand($this->getOptions()),
            'collection' => $this->makeCollectionCommand($this->getOptions()),
            'settings_voices' => $this->makeSettingsVoicesCommand($this->getOptions()),
            'settings_silent' => $this->makeSettingsSilentCommand($this->getOptions()),
            'settings_priority' => $this->makeSettingsPriorityCommand($this->getOptions()),
            'del' => $this->makeDelCommand($this->getOptions()),
            'empty' => $this->makeEmptyCommand($this->getOptions()),
        };
    }

    private function makeAlarmCommand(CommandOptions $options): CommandInterface
    {
        return (new AlarmService($options))->validate(new AlarmValidator());
    }

    private function makeCollectionCommand(CommandOptions $options): CommandInterface
    {
        return new CollectionService($options);
    }

    private function makeEmptyCommand(CommandOptions $options)
    {
        return new EmptyCallbackService($options);
    }

    private function makeSettingsVoicesCommand(CommandOptions $options)
    {
        return new SettingsVoicesService($options);
    }

    private function makeSettingsSilentCommand(CommandOptions $options)
    {
        return new SettingsSilentService($options);
    }

    private function makeSettingsPriorityCommand(CommandOptions $options)
    {
        return new SettingsPriorityService($options);
    }

    private function makeDelCommand(CommandOptions $options)
    {
        return (new DelService($options))->validate(new DelValidator());
    }
}
