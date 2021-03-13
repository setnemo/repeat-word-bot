<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
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

    /**
     * BaseCommandService constructor.
     *
     * @param CommandOptions $options
     */
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
     * {@inheritDoc}
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

    /**
     * {@inheritDoc}
     * @throws TelegramException
     */
    public function postStackMessages(): CommandInterface
    {
        /** @var ResponseDirector $response */
        foreach ($this->stack as $response) {
            $response->getResponse();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     * @throws TelegramException
     */
    public function getResponseMessage(): ServerResponse
    {
        return null === $this->response ? Request::emptyResponse() : $this->response->getResponse();
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

    /**
     * @return bool
     */
    public function hasResponse(): bool
    {
        return [] !== $this->stack || null !== $this->response;
    }
    
    /**
     * {@inheritDoc}
     */
    public function showResponses(): array
    {
        return array_merge($this->stack, [$this->response]);
    }
}
