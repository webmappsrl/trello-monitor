<?php

namespace Database\Factories;

use App\Models\TrelloBoard;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrelloBoardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrelloBoard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(2),
            'trello_id' => $this->faker->unique()->sha1()
        ];
    }
}
