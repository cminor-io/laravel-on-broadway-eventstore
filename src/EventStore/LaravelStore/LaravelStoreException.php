<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Broadway\EventStore\EventStoreException;

/**
 * Defines an exception when something has gone wrong with the Laravel store.
 *
 * Class LaravelStoreException
 * @package CminorIO\LaravelOnBroadway\EventStore\LaravelStore
 */
class LaravelStoreException extends EventStoreException
{

    /**
     * LaravelStoreException constructor.
     * Defines an exception for when something went wrong with the store.
     *
     * @param string $message The exception message.
     * @param \Exception $previous OPTIONAL The previous exception.
     */
    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, '500', $previous);
    }
}
