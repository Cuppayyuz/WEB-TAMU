<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BukaCanvasEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function broadcastOn(): array
    {
        return [
            new Channel('buku-tamu'),
        ];
    }

    // TAMBAHKAN KODE INI
    public function broadcastAs()
    {
        return 'BukaCanvasEvent';
    }
}