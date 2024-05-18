<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CommentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = $this->data;
        $to = User::find($event->userId);
        // dd($event->sender);
        $msg = $event->description;
        $content = array(
            "en" => $msg
        );
        $headings = array(
            "en" => $event->username." Wrote on your wall"
        );
        if (!$event->imageUrl) {
            $fields = array(
                'app_id' => '32945f51-424b-4932-a5cc-f5dc0b54937c',
                "headings" => $headings,
                // 'included_segments' => array('All'),
                'include_player_ids' => $to->deviceId,
                'large_icon' => "ic_launcher_round.png",
                'content_available' => true,
                'contents' => $content
            );
        } else {
            $ios_img = array(
                "id1" => $event->imageUrl
            );
            $fields = array(
                'app_id' => '32945f51-424b-4932-a5cc-f5dc0b54937c',
                "headings" => $headings,
                // 'included_segments' => array('All'),
                'include_player_ids' => array($to->deviceId),
                'contents' => $content,
                // "big_picture" => $event->sender->imageUrl,
                'large_icon' => "ic_launcher_round.png",
                'content_available' => true,
                // "ios_attachments" => $ios_img
            );
        }

        // if( isset($oneSignalTemplateID) && $oneSignalTemplateID != '' ) {
        //     $fields['template_id'] = $oneSignalTemplateID;
        // }

        $headers = array(
            'Authorization: Basic MjAwMjhlN2ItNGJiYi00ODg2LWE5ZTgtY2NiNzQzNTk2MGIz',
            'Content-Type: application/json; charset=utf-8'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        // return $result;
    }
}
