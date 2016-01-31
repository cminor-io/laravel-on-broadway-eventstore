<?php

namespace CminorIO\LaravelOnBroadway\EventStore\LaravelStore;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainEventStreamInterface;
use Broadway\Domain\DomainMessage;
use Broadway\Serializer\SerializationException;
use Broadway\Serializer\SerializerInterface;

/**
 * Serves as convenient serializer for domain messages.
 * Essentially it groups the serialization / deserialization of the different
 * parts of a domain message.
 *
 * Class SimpleStreamSerializer
 * @package CminorIO\LaravelOnBroadway\EventStore\LaravelStore
 */
class SimpleStreamSerializer implements StreamSerializerInterface
{

    /**
     * Your broadway payload serializer.
     *
     * @var SerializerInterface
     */
    private $payloadSerializer;

    /**
     * Your broadway metadata serializer.
     *
     * @var SerializerInterface
     */
    private $metadataSerializer;

    /**
     * SimpleStreamSerializer constructor.
     * Initializes the serializer as a composite serializer of broadway specific
     * serializers.
     *
     * @param SerializerInterface $payloadSerializer  The domain message payload
     *                                                serializer.
     * @param SerializerInterface $metadataSerializer The domain message metadata
     *                                                serializer.
     */
    public function __construct(
        SerializerInterface $payloadSerializer,
        SerializerInterface $metadataSerializer
    ) {
        $this->payloadSerializer = $payloadSerializer;
        $this->metadataSerializer = $metadataSerializer;
    }

    /**
     * Converts a stream of DomainMessages into array representation so that
     * it can be persisted by laravel db connector.
     *
     * @param DomainEventStreamInterface $stream The stream of events.
     * @throws SerializationException            When something went wrong...
     * @return array                             An array of domain messages in
     *                                           pure php array format.
     */
    public function serialize(DomainEventStreamInterface $stream)
    {
        $records = [];
        foreach ($stream as $message) {
            $records[] = $this->convertDomainMessage($message);
        }

        return $records;
    }

    /**
     * Turns a DomainMessage into array using broadway's serializers.
     *
     * @param DomainMessage $message The domain message.
     * @throws SerializationException When the serializers cannot perform.
     * @return array                  An array representation of the message.
     */
    private function convertDomainMessage(DomainMessage $message)
    {
        $metadata = $this->metadataSerializer->serialize($message->getMetadata());
        $payload = $this->payloadSerializer->serialize($message->getPayload());

        return [
            'uuid' => (string)$message->getId(),
            'playhead' => $message->getPlayhead(),
            'metadata' => json_encode($metadata),
            'payload' => json_encode($payload),
            'recorded_on' => $message->getRecordedOn()->toString(),
            'type' => $message->getType()
        ];
    }

    /**
     * Hydrates a DomainEventStream from an array of database records.
     *
     * @param array $records The array representation of
     *                                    the domain messages.
     * @throws SerializationException     When the serializers cannot perform.
     * @return DomainEventStreamInterface A DomainEventStream.
     */
    public function deserialize(array $records)
    {
        $payloadSerializer = $this->payloadSerializer;
        $metaSerializer = $this->metadataSerializer;

        $messages = array_map(
            function ($row) use ($payloadSerializer, $metaSerializer) {

                // laravel db returns stdClass objects
                $row = (array)$row;

                // Unfortunately Broadway's default implementation require this.
                return new DomainMessage(
                    $row['uuid'],
                    $row['playhead'],
                    $metaSerializer->deserialize(
                        json_decode($row['metadata'], true)
                    ),
                    $payloadSerializer->deserialize(
                        json_decode($row['payload'], true)
                    ),
                    DateTime::fromString($row['recorded_on'])
                );

            },
            $records
        );

        return new DomainEventStream($messages);
    }
}
