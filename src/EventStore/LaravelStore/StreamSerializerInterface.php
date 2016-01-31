<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Broadway\Domain\DomainEventStreamInterface;

/**
 * Interface StreamSerializerInterface
 * Defines the serializer required by LaravelStore to serialize/deserialize
 * DomainMessageStreams.
 *
 * @package CminorIO\LaravelOnBroadway\EventStore\LaravelStore
 */
interface StreamSerializerInterface
{

    /**
     * Serializes a DomainEventStreamInterface into an array of php arrays
     * that represent the domain event messages.
     *
     * @param DomainEventStreamInterface $stream The event stream.
     * @return array                             An array of assoc arrays.
     */
    public function serialize(DomainEventStreamInterface $stream);

    /**
     * Hydrates a DomainEventStreamInterface out of database records in array
     * format representing the domain messages.
     *
     * @param array $records              The domain messages in an array
     *                                    format, as found in the database.
     * @return DomainEventStreamInterface The reconstituted domain event stream.
     */
    public function deserialize(array $records);
}
