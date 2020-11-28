<?php

namespace Database\Factories;

use App\Models\TrelloList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrelloListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrelloList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'trello_id' => $this->faker->unique()->sha1()
        ];
    }
}
