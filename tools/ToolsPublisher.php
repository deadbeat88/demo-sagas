<?php

declare(strict_types = 1);

use ServiceBus\MessageSerializer\MessageEncoder;
use ServiceBus\MessageSerializer\Symfony\SymfonyMessageSerializer;
use ServiceBus\Transport\Amqp\AmqpTransportLevelDestination;
use ServiceBus\Transport\Common\Package\OutboundPackage;
use ServiceBus\Transport\Common\Transport;
use ServiceBus\Transport\PhpInnacle\PhpInnacleTransport;
use Symfony\Component\Dotenv\Dotenv;
use function Amp\Promise\wait;
use function ServiceBus\Common\uuid;

/**
 * Tools message publisher
 *
 * For tests only
 */
final class ToolsPublisher
{
    /**
     * @var Transport|null
     */
    private $transport;

    /**
     * @var MessageEncoder
     */
    private $encoder;

    /**
     * @param string $envPath
     */
    public function __construct(string $envPath)
    {
        (new Dotenv())->load($envPath);

        $this->encoder = new SymfonyMessageSerializer();
    }

    /**
     * Send message to queue
     *
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param object      $message
     * @param string|null $traceId
     * @param string|null $topic
     * @param string|null $routingKey
     *
     * @return void
     */
    public function sendMessage(
        object $message,
        string $traceId = null,
        ?string $topic = null,
        ?string $routingKey = null
    ): void {
        $traceId    = $traceId ?? uuid();
        $topic      = (string)($topic ?? \getenv('TRANSPORT_TOPIC'));
        $routingKey = (string)($routingKey ?? \getenv('TRANSPORT_ROUTING_KEY'));

        /** @noinspection PhpUnhandledExceptionInspection */
        wait(
            $this->transport()->send(
                new OutboundPackage(
                    $this->encoder->encode($message),
                    [Transport::SERVICE_BUS_TRACE_HEADER => $traceId],
                    new AmqpTransportLevelDestination($topic, $routingKey),
                    $traceId
                )
            )
        );
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @return Transport
     */
    private function transport(): Transport
    {
        if (null === $this->transport) {
            $this->transport = new PhpInnacleTransport(
                new \ServiceBus\Transport\Amqp\AmqpConnectionConfiguration((string)\getenv('TRANSPORT_CONNECTION_DSN'))
            );

            /** @noinspection PhpUnhandledExceptionInspection */
            wait($this->transport->connect());
        }

        return $this->transport;
    }
}
