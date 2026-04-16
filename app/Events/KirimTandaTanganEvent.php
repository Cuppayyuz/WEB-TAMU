<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class KirimTandaTanganEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $tandaTanganBase64;

    public function __construct($tandaTanganBase64)
    {
        $this->tandaTanganBase64 = $tandaTanganBase64;
    }

   // ... kode sebelumnya ...
    public function broadcastOn(): array
    {
        return [
            new Channel('buku-tamu'),
        ];
    }

    // TAMBAHKAN KODE INI
    public function broadcastAs()
    {
        return 'KirimTandaTanganEvent';
    }
}