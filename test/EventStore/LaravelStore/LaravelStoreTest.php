<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Broadway\EventStore\EventStoreTest;
use Broadway\Serializer\SimpleInterfaceSerializer;
use Illuminate\Database\Connectors\SQLiteConnector;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;

/**
 * The test case for the laravel store.
 * It runs the exact same tests as the rest of the broadway event stores to
 * ensure compatibility.
 *
 * @requires extension pdo_sqlite
 */
class LaravelStoreTest extends EventStoreTest
{
    /**
     * Instantiates the Laravel event store.
     */
    public function setUp()
    {
        // Create a mock database connection.
        $connection = $this->getMockConnection();

        // Create the serializer for the domain event streams using the
        // broadway default serializers.
        $streamSerializer = new SimpleStreamSerializer(
            new SimpleInterfaceSerializer(),
            new SimpleInterfaceSerializer()
        );

        $tableName = 'events';

        // Instantiate the laravel store.
        $eventStore = new LaravelStore(
            $connection,
            $streamSerializer,
            $tableName
        );

        // Create the schema for the event store.
        $schemaBuilder = $connection->getSchemaBuilder();
        $schemaBuilder->create($tableName, function (Blueprint $table) {
            return LaravelStoreSchema::describeSchema($table);
        });

        $this->eventStore = $eventStore;

    }

    /**
     * Create an sqllite in memory connection.
     *
     * @return SQLiteConnection
     */
    private function getMockConnection()
    {

        $pdo = function () {
            $config = [
                'database' => ':memory:',
                'prefix' => '',
            ];
            $connector = new SQLiteConnector();

            return $connector->connect($config);
        };

        return new SQLiteConnection($pdo, ':memory:', '');
    }
}
