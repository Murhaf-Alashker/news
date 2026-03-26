<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facebook' => 'www.facebook.com',
            'instagram' => 'www.instagram.com',
            'linkedin' => 'www.linkedin.com',
            'whatsapp' => fake()->phoneNumber(),
            'response_email' => fake()->safeEmail()
        ];
    }
}
