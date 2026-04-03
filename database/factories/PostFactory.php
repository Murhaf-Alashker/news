<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = ['approved', 'pending','rejected','approved','pending','approved'];
        return [
            'title' => fake()->sentence(1),
            'ulid' => Str::ulid(),
            'description' => fake()->text,
            'user_id' => User::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'status' => $status[array_rand($status)],
            'views' => rand(0, 1000),
            'likes' => rand(0, 1000),
            'dislikes' => rand(0, 1000),
            'is_featured' => rand(0, 1),

        ];
    }
}
