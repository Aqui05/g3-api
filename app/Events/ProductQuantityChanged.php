<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;



class ProductQuantityChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    public $originalQuantity;
    public $newQuantity;

    /**
     * Create a new event instance.
     */
    public function __construct($product, $originalQuantity, $newQuantity)
    {
        $this->product = $product;
        $this->originalQuantity = $originalQuantity;
        $this->newQuantity = $newQuantity;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }

}
