<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $senderId = $this->faker->randomElement([0,1]);
        if($senderId === 0 ){
            $senderId=$this->faker->randomElement(User::where('id',"!=",1)->pluck("id")->toArray());
            $receiverId=1;
        }else{
            $receiverId=$this->faker->randomElement(User::pluck("id")->toArray());
        }

        return [
            "message"=>$this->faker->realText(100),
            "senderId"=>$senderId,
            "receiverId"=>$receiverId,
            // "conversationId",
            "created_at"=>$this->faker->dateTimeBetween("-1 year","now"),
        ];
    }
}
