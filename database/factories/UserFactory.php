<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                // 'imageUrl' => 'https://media.sproutsocial.com/uploads/2022/06/profile-picture.jpeg',
                'imageUrl' => 'https://picsum.photos/id/'.rand(1,500).'/300/500',
                'wallpaperUrl' => 'https://picsum.photos/id/'.rand(1,500).'/500/300/?blur',
                // 'wallpaperUrl' => 'https://cdn.pixabay.com/photo/2022/01/28/18/32/leaves-6975462_1280.png',
                'link' => $this->faker->url(),
                'location' => $this->faker->streetName(),
                'birthday' => $this->faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now'),
                'isModel' => rand(0,1),
                'occupation' => $this->faker->jobTitle(),
                'bio' => $this->faker->realText($maxNbChars = 200, $indexSize = 2),
                // 'gender' => $this->faker->title('male'|'female') ,
                'lat' => $this->faker->latitude($min = -90, $max = 90)  ,
                'lng' => $this->faker->longitude($min = -180, $max = 180) ,
                // 'userSettingsId' => rand(1,20),
                'email_verified_at'=> now(),
                'otp'=> '0',
                'isActive'=> '1',
                'password' => static::$password ??= Hash::make('password'),
                'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
