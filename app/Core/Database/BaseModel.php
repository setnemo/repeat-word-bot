<?php

declare(strict_types=1);

namespace RepeatBot\Core\Database;

/**
 * Class BaseModel
 * @package RepeatBot\Core\Database
 */
class BaseModel
{
    private bool $emptyModel = false;

    /**
     * BaseModel constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties)
    {
        if (empty($properties)) {
            $this->emptyModel = true;
        } else {
            foreach ($properties as $property => $value) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->emptyModel;
    }
}
