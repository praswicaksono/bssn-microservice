<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Payment;
use Hyperf\Jet\AbstractClient;
use Hyperf\Jet\ClientFactory;
use Hyperf\Jet\DataFormatter\DataFormatter;
use Hyperf\Jet\Packer\JsonEofPacker;
use Hyperf\Jet\PathGenerator\PathGenerator;
use Hyperf\Jet\ProtocolManager;
use Hyperf\Jet\ServiceManager;
use Hyperf\Jet\Transporter\GuzzleHttpTransporter;
use Hyperf\RpcServer\Annotation\RpcService;

#[RpcService(name: "PaymentService", protocol: "jsonrpc-http", server: "jsonrpc-http")]
final class PaymentService
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

        ServiceManager::register($bookingService = 'BookingService', $protocol, [
            ServiceManager::NODES => [
                ['booking-service', 9505],
            ],
        ]);

        $this->client = (new ClientFactory())->create($bookingService, $protocol);
    }

    public function pay(int $bookingId, int $amount)
    {
        $payment = new Payment();
        $payment->booking_id = $bookingId;
        $payment->amount = $amount;
        $payment->status = 'paid';
        $payment->save();

        $this->client->acceptBooking($bookingId);
    }
}
