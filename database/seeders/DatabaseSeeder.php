<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\StatusChannel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Favorite;
use App\Models\BlockList;
use App\Models\Following;
use App\Models\UserSettings;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $umair = [
            'name' => 'umairabbasbhatti719',
            'email' => 'umairabbasbhatti719@gmail.com',
            // 'imageUrl' => 'https://media.sproutsocial.com/uploads/2022/06/profile-picture.jpeg',
            'imageUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/300/500',
            'wallpaperUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/500/300/?blur',
            'password' => Hash::make('password'),
            'lat' => '82.012535',
            'lng' => '-27.104958',
            'isModel' => '1',
            // 'userSettingsId' => 1,
            'email_verified_at' => now(),
            'otp' => '0',
            'isActive' => '1',
        ];
        $client = [
            'name' => 'shamrock films',
            'email' => 'shamrockfilms@gmail.com',
            // 'imageUrl' => 'https://media.sproutsocial.com/uploads/2022/06/profile-picture.jpeg',
            'imageUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/300/500',
            'wallpaperUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/500/300/?blur',
            'password' => Hash::make('password'),
            'lat' => '82.012515',
            'lng' => '-27.104938',
            'isModel' => '1',
            // 'userSettingsId' => 1,
            'email_verified_at' => now(),
            'otp' => '0',
            'isActive' => '1',
        ];
        $sup = [
            'name' => 'Talha',
            'email' => 'talhaprince09@gmail.com',
            // 'imageUrl' => 'https://media.sproutsocial.com/uploads/2022/06/profile-picture.jpeg',
            'imageUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/300/500',
            'wallpaperUrl' => 'https://picsum.photos/id/' . rand(1, 500) . '/500/300/?blur',
            'password' => Hash::make('password'),
            'lat' => '82.012545',
            'lng' => '-27.104968',
            'isModel' => '1',
            // 'userSettingsId' => 1,
            'email_verified_at' => now(),
            'otp' => '0',
            'isActive' => '1',
        ];

        UserSettings::factory(200)->create();
        User::create($sup);
        User::create($umair);
        User::create($client);
        User::factory(200)->create();
        StatusChannel::factory(1)->create();
        StatusChannel::create([
            'userId' => 2,
            'lastPostId' => 0,
        ]);
        Following::factory(100)->create();
        StatusChannel::create([
            'userId' => 3,
            'lastPostId' => 0,
        ]);
        Following::factory(100)->create();
        BlockList::factory(100)->create();
        Favorite::factory(100)->create();

        // Message::factory(500)->create();

        // $conversations=Message::all()->groupBy(function ($msg) {
        //     return collect([$msg->senderId,$msg->receiverId])->sort()->implode('_');
        // })->map(function ($grpMsg) {
        //     return [
        //         "userId1"=> $grpMsg->first()->senderId,
        //         "userId2"=> $grpMsg->first()->receiverId,
        //         "lastMessageId"=> $grpMsg->last()->id,
        //         "created_at"=>new Carbon(),
        //         "updated_at"=>new Carbon(),
        //     ];
        // })->values();

        // Conversation::insertOrIgnore($conversations->toArray());
    }
}
