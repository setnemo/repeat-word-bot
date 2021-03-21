<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService;

use RepeatBot\Bot\Service\CommandService\Commands\AlarmService;
use RepeatBot\Bot\Service\CommandService\Commands\CollectionService;
use RepeatBot\Bot\Service\CommandService\Commands\DelService;
use RepeatBot\Bot\Service\CommandService\Commands\ExportService;
use RepeatBot\Bot\Service\CommandService\Commands\HelpService;
use RepeatBot\Bot\Service\CommandService\Commands\ProgressService;
use RepeatBot\Bot\Service\CommandService\Commands\ResetService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsPriorityService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsSilentService;
use RepeatBot\Bot\Service\CommandService\Commands\SettingsVoicesService;
use RepeatBot\Bot\Service\CommandService\Commands\StartService;
use RepeatBot\Bot\Service\CommandService\Commands\TimeService;
use RepeatBot\Bot\Service\CommandService\Commands\TrainingService;
use RepeatBot\Bot\Service\CommandService\Commands\TranslateTrainingService;
use RepeatBot\Bot\Service\CommandService\Validators\AlarmValidator;
use RepeatBot\Bot\Service\CommandService\Validators\DelProgressValidator;
use RepeatBot\Bot\Service\CommandService\Validators\ExportValidator;
use RepeatBot\Bot\Service\CommandService\Validators\ResetProgressValidator;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\Service\EmptyCallbackService;

class CommandDirector extends \TelegramBot\CommandWrapper\Command\CommandDirector
{
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
        return (new AlarmService($options))->validate(new AlarmValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeCollectionCommand(CommandOptions $options): CommandInterface
    {
        return new CollectionService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeEmptyCommand(CommandOptions $options): CommandInterface
    {
        return new EmptyCallbackService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsVoicesCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsVoicesService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsSilentCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsSilentService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeSettingsPriorityCommand(CommandOptions $options): CommandInterface
    {
        return new SettingsPriorityService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeDelCommand(CommandOptions $options): CommandInterface
    {
        return (new DelService($options))->validate(new DelProgressValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeExportCommand(CommandOptions $options): CommandInterface
    {
        return (new ExportService($options))->validate(new ExportValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeHelpCommand(CommandOptions $options): CommandInterface
    {
        return new HelpService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeProgressCommand(CommandOptions $options): CommandInterface
    {
        return new ProgressService($options);
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeResetCommand(CommandOptions $options): CommandInterface
    {
        return (new ResetService($options))->validate(new ResetProgressValidator());
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTrainingCommand(CommandOptions $options): CommandInterface
    {
        return (new TrainingService($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTranslateTrainingCommand(CommandOptions $options): CommandInterface
    {
        return (new TranslateTrainingService($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeStartCommand(CommandOptions $options): CommandInterface
    {
        return (new StartService($options));
    }

    /**
     * @param CommandOptions $options
     *
     * @return CommandInterface
     */
    private function makeTimeCommand(CommandOptions $options): CommandInterface
    {
        return (new TimeService($options));
    }
}
