<?php

namespace Database\Factories;

use App\Models\TrelloBoard;
use App\Models\TrelloCard;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrelloCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TrelloCard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name(),
            'trello_id' => $this->faker->unique()->sha1(),
            'board_id' => \App\Models\TrelloBoard::factory(),
            'list_id' => \App\Models\TrelloList::factory(),
            'member_id' => \App\Models\TrelloMember::factory(),
        ];
    }
}
