<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScanRFIDEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cardID;
  
    public function __construct($cardID)
    {
        $this->cardID = $cardID;
    }
  
    public function broadcastOn()
    {
        return ['ch-scan-rfid'];
    }
  
    public function broadcastAs()
    {
        return 'evt-scan-rfid';
    }
}
