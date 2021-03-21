<?php

declare(strict_types=1);

namespace RepeatBot\Bot\Service\CommandService\Commands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use RepeatBot\Core\App;
use RepeatBot\Core\Cache;
use RepeatBot\Core\Metric;
use TelegramBot\CommandWrapper\Command\BaseDefaultCommandService as DefaultCommandService;
use TelegramBot\CommandWrapper\Command\CommandInterface;
use TelegramBot\CommandWrapper\Command\CommandOptions;
use TelegramBot\CommandWrapper\ResponseDirector;
use TelegramBot\CommandWrapper\Validator\ValidateCommand;

abstract class BaseDefaultCommandService extends DefaultCommandService
{
    protected Cache $cache;

    /**
     * BaseCommandService constructor.
     *
     * @param CommandOptions $options
     */
    public function __construct(CommandOptions $options)
    {
        parent::__construct($options);
        $config = App::getInstance()->getConfig();
        $this->cache = Cache::getInstance()->init($config);
        Metric::getInstance()->init($config)->increaseMetric('usage');
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
