<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainEventStreamInterface;
use Broadway\EventStore\EventStoreInterface;
use Broadway\EventStore\EventStreamNotFoundException;
use Illuminate\Database\ConnectionInterface;

/**
 * A Broadway event store implementation using native laravel db driver.
 *
 * Class LaravelStore
 * @package CminorIO\LaravelOnBroadway\EventStore\LaravelStore
 */
class LaravelStore implements EventStoreInterface
{

    /**
     * The laravel database connector.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * A convenient serializer that serializes/unserializes an message stream
     * for laravel event store consumption.
     *
     * @var StreamSerializerInterface
     */
    protected $streamSerializer;

    /**
     * The name of the db table.
     *
     * @var string
     */
    protected $tableName;

    /**
     * LaravelStore constructor.
     * Instantiates a laravel store instance.
     *
     * @param ConnectionInterface $connection             The database connector.
     * @param StreamSerializerInterface $streamSerializer The message stream
     *                                                    serializer.
     * @param string $tableName                           The table name.
     */
    public function __construct(
        ConnectionInterface $connection,
        StreamSerializerInterface $streamSerializer,
        $tableName
    ) {
        $this->connection = $connection;
        $this->streamSerializer = $streamSerializer;
        $this->tableName = $tableName;
    }

    /**
     * Returns a DomainEventStream associated with the identifier.
     *
     * @param mixed $identifier             The identifier of the aggregate.
     * @throws \InvalidArgumentException    When the identifier is not provided.
     * @throws EventStreamNotFoundException When no stream can be found for the
     *                                      provided aggregate identifier.
     * @return DomainEventStream            A collection of domain messages.
     */
    public function load($identifier)
    {

        // Retrieve the serialized stream records from the database.
        $records = $this->connection
            ->table($this->tableName)
            ->where('uuid', '=', (string)$identifier)
            ->orderBy('playhead', 'asc')
            ->get();

        // Ensure we do have records for this aggregate.
        if (count($records) === 0) {
            $message = sprintf(
                'EventStream not found for aggregate with id %s',
                $identifier
            );
            throw new EventStreamNotFoundException($message);
        }

        // Reconstitute the domain event stream.
        return $this->streamSerializer->deserialize($records);
    }

    /**
     * Appends the message stream to the event store.
     *
     * @param mixed $identifier                  The aggregate identifier.
     * @param DomainEventStreamInterface $stream The stream of domain messages.
     * @throws LaravelStoreException             When something went wrong
     *                                           during persistence.
     * @return void
     */
    public function append($identifier, DomainEventStreamInterface $stream)
    {

        // No-op to ensure that an error will be thrown early if the ID
        // is not something that can be converted to a string.
        (string) $identifier;

        try {
            $this->connection->beginTransaction();

            // Make the domain messages database friendly..
            $records = $this->streamSerializer->serialize($stream);

            // Now its time to persist them..
            foreach ($records as $record) {
                $this->connection
                    ->table($this->tableName)
                    ->insert($record);
            }

            $this->connection->commit();

        } catch (\Exception $exception) {
            // Oops, something went wrong
            $this->connection->rollBack();

            $message = 'Error while persisting the domain events';
            throw new LaravelStoreException($message, $exception);
        }
    }
}
