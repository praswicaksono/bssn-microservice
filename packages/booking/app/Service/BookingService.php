<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Booking;
use Hyperf\Jet\AbstractClient;
use Hyperf\Jet\ClientFactory;
use Hyperf\Jet\DataFormatter\DataFormatter;
use Hyperf\Jet\Packer\JsonEofPacker;
use Hyperf\Jet\PathGenerator\PathGenerator;
use Hyperf\Jet\ProtocolManager;
use Hyperf\Jet\ServiceManager;
use Hyperf\Jet\Transporter\GuzzleHttpTransporter;
use Hyperf\RpcServer\Annotation\RpcService;

#[RpcService(name: "BookingService", protocol: "jsonrpc-http", server: "jsonrpc-http")]
final class BookingService
{
    private readonly AbstractClient $client;

    public function __construct()
    {
        ProtocolManager::register($protocol = 'jsonrpc', [
            ProtocolManager::TRANSPORTER => new GuzzleHttpTransporter(),
            ProtocolManager::PACKER => new JsonEofPacker(),
            ProtocolManager::PATH_GENERATOR => new PathGenerator(),
            ProtocolManager::DATA_FORMATTER => new DataFormatter(),
        ]);

        ServiceManager::register($eventService = 'EventService', $protocol, [
            ServiceManager::NODES => [
                ['event-service', 9505],
            ],
        ]);

        $this->client = (new ClientFactory())->create($eventService, $protocol);

    }

    public function test()
    {
        return;
    }

    public function acceptBooking(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->status = 'accepted';
        $booking->save();
    }

    public function bookEvent(int $eventId): void
    {
        if ($this->client->isQuotaFull($eventId)) {
            throw new \Exception("Event quota full");
        }

        $booking = new Booking();
        $booking->event_id = $eventId;
        $booking->save();

        $this->client->decreaseQuota($eventId);
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->status = 'rejected';
        $booking->save();
    }
}
