<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Credential;
use App\Models\User;
use App\Models\UserCategory;

class CredentialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Credential::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->name(),
            'username' => fake()->userName(),
            'password' => fake()->password(),
            'salt' => fake()->word(),
            'notes' => fake()->text(),
            'shareable' => fake()->boolean(),
        ];
    }
}
