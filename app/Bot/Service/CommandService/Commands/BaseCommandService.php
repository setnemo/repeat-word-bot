<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use RepeatBot\Bot\Service\CommandService\CommandOptions;
use RepeatBot\Bot\Service\CommandService\ResponseDirector;
use RepeatBot\Bot\Service\CommandService\Validators\ValidateCommand;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;

abstract class BaseCommandService implements CommandInterface
{
    protected CommandOptions $options;

    protected Cache $cache;

    protected array $stack = [];

    protected ?ResponseDirector $response = null;

    public function __construct(CommandOptions $options)
    {
        $this->options = $options;
        $config = App::getInstance()->getConfig();
        $this->cache = Cache::getInstance()->init($config);
    }

    /**
     * @return CommandOptions
     */
    public function getOptions(): CommandOptions
    {
        return $this->options;
    }

    /**
     * @param ValidateCommand $validator
     *
     * @return CommandInterface
     */
    public function validate(?ValidateCommand $validator): CommandInterface
    {
        if (null !== $validator) {
            $errors = $validator->validate($this->getOptions());
            if (count($errors) > 0) {
                $this->setResponse(array_pop($errors));
                if (count($errors) > 0) {
                    foreach ($errors as $error) {
                        $this->addStackMessage($error);
                    }
                }
            }
        }

        return $this;
    }

    public function postStackMessages(): CommandInterface
    {
        /** @var ResponseDirector $response */
        foreach ($this->stack as $response) {
            $response->getResponse();
        }

        return $this;
    }

    /**
     * @return ServerResponse
     * @throws TelegramException
     */
    public function getResponseMessage(): ServerResponse
    {
        return $this->response->getResponse();
    }

    /**
     * @param ResponseDirector $response
     */
    protected function setResponse(ResponseDirector $response): void
    {
        $this->response = $response;
    }

    /**
     * @param ResponseDirector $response
     */
    public function addStackMessage(ResponseDirector $response): void
    {
        $this->stack[] = $response;
    }

    public function hasResponse(): bool
    {
        return [] !== $this->stack || null !== $this->response;
    }
}
