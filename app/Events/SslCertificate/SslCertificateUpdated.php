<?php

namespace App\Events\SslCertificate;

use App\Models\SslCertificate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class SslCertificateUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ssl_certificate;

    /**
     * Create a new event instance.
     *
     * @param SslCertificate $sslCertificate
     */
    public function __construct(SslCertificate $sslCertificate)
    {
        $this->ssl_certificate = $sslCertificate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.SslCertificate.'.$this->ssl_certificate->id);
    }
}
