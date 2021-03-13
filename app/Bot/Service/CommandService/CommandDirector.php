<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use RepeatBot\Bot\Service\CommandService\Commands\AlarmServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\CollectionServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\CommandInterface;
use RepeatBot\Bot\Service\CommandService\Commands\DelServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\EmptyCallbackServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\ExportServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\HelpServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\ProgressServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\ResetServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsPriorityServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsSilentServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsVoicesServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\StartServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\TimeServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\TrainingServiceDefault;
use RepeatBot\Bot\Service\CommandService\Commands\TranslateTrainingServiceDefault;
use RepeatBot\Bot\Service\CommandService\Validators\AlarmValidator;
use RepeatBot\Bot\Service\CommandService\Validators\DelProgressValidator;
use RepeatBot\Bot\Service\CommandService\Validators\ExportValidator;
use RepeatBot\Bot\Service\CommandService\Validators\ResetProgressValidator;

class CommandDirector
{
    /**
     * CommandDirector constructor.
     *
     * @param CommandOptions $options
     */
    public function __construct(protected CommandOptions $options)
    {
    }

    /**
     * @return CommandOptions
     */
    public function getOptions(): CommandOptions
    {
        return $this->options;
    }

    /**
     * @return CommandInterface
     */
    public function makeService(): CommandInterface
    {
        return match($this->getOptions()->getCommand()) {
            'alarm'              => $this->makeAlarmCommand($this->getOptions()),
            'collections'        => $this->makeCollectionCommand($this->getOptions()),
            'empty'              => $this->makeEmptyCommand($this->getOptions()),
            'settings'           => $this->makeSettingsCommand($this->getOptions()),
            'settings_voices'    => $this->makeSettingsVoicesCommand($this->getOptions()),
            'settings_silent'    => $this->makeSettingsSilentCommand($this->getOptions()),
            'settings_priority'  => $this->makeSettingsPriorityCommand($this->getOptions()),
            'del'                => $this->makeDelCommand($this->getOptions()),
            'export'             => $this->makeExportCommand($this->getOptions()),
            'help'               => $this->makeHelpCommand($this->getOptions()),
            'progress'           => $this->makeProgressCommand($this->getOptions()),
            'reset'              => $this->makeResetCommand($this->getOptions()),
            'training'           => $this->makeTrainingCommand($this->getOptions()),
            'translate_training' => $this->makeTranslateTrainingCommand($this->getOptions()),
            'start'              => $this->makeStartCommand($this->getOptions()),
            'time'               => $this->makeTimeCommand($this->getOptions()),
        };
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeAlarmCommand(CommandOptions $options): CommandInterface
    {
        return (new AlarmServiceDefault($options))->validate(new AlarmValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeCollectionCommand(CommandOptions $options): CommandInterface
    {
        return new CollectionServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeEmptyCommand(CommandOptions $options): CommandInterface
    {
        return new EmptyCallbackServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsVoicesCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsVoicesServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsSilentCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsSilentServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsPriorityCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsPriorityServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeDelCommand(CommandOptions $options): CommandInterface
    {
        return (new DelServiceDefault($options))->validate(new DelProgressValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeExportCommand(CommandOptions $options): CommandInterface
    {
        return (new ExportServiceDefault($options))->validate(new ExportValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeHelpCommand(CommandOptions $options): CommandInterface
    {
        return new HelpServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeProgressCommand(CommandOptions $options): CommandInterface
    {
        return new ProgressServiceDefault($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeResetCommand(CommandOptions $options): CommandInterface
    {
        return (new ResetServiceDefault($options))->validate(new ResetProgressValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTrainingCommand(CommandOptions $options): CommandInterface
    {
        return (new TrainingServiceDefault($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTranslateTrainingCommand(CommandOptions $options): CommandInterface
    {
        return (new TranslateTrainingServiceDefault($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeStartCommand(CommandOptions $options): CommandInterface
    {
        return (new StartServiceDefault($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTimeCommand(CommandOptions $options): CommandInterface
    {
        return (new TimeServiceDefault($options));
    }
}
