<?php

namespace App\Console\Commands;

use App\Events\CommentEvent;
use App\Events\TestEvent;
use App\Events\TypingEvent;
use Illuminate\Console\Command;
use App\Events\ChannelUpdatesEvent;

class FireEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fire:event {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // CommentEvent::dispatch($this->argument('name'));
        // ChannelUpdatesEvent::dispatch($this->argument('name'));
        // TypingEvent::dispatch([
        //     "user1Id" => 'id1',
        //     "user2Id" => 'id2'
        // ]);
        // TestEvent::dispatch($this->argument('name'));
        
    }
}
