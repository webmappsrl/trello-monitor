<?php

namespace Database\Factories;

use App\Models\TrelloMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrelloMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrelloMember::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'trello_id' => $this->faker->unique()->sha1()
        ];
    }
}
