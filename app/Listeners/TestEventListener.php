<?php

namespace App\Listeners;

use App\Models\Comment;
use App\Events\TestEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TestEvent $event): void
    {
            // $user = auth()->user();
            // $r =[];
            // $r['userId'] = $user->id;
            // $r['description'] = $event->msg;
            // $r['imageUrl'] = $user->imageUrl;
            // $r['username'] = $user->name;

            // Comment::create($r);
    }
}
