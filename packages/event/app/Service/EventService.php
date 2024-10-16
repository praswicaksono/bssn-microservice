<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Event;
use Hyperf\RpcServer\Annotation\RpcService;
use Psr\Log\LoggerInterface;

#[RpcService(name: "EventService", protocol: "jsonrpc-http", server: "jsonrpc-http")]
final class EventService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function isQuotaFull(int $eventId): bool
    {
        $this->logger->info('isQuotaFull called', ['eventId' => $eventId]);
        $event = Event::findOrFail($eventId);

        return $event->quota <= 0;
    }

    public function decreaseQuota(int $eventId): void
    {
        $this->logger->info('decreaseQuota called', ['eventId' => $eventId]);
        $event = Event::findOrFail($eventId);
        $event->quota--;
        $event->save();
    }

    public function inceaseQuota(int $eventId): void
    {
        $this->logger->info('increaseQuota called', ['eventId' => $eventId]);
        $event = Event::findOrFail($eventId);
        $event->quota++;
        $event->save();
    }
}
