<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name,
            'description' => $this->faker->paragraph,
            'board' => $this->faker->randomKey([
                'SISTECO' => 'SISTECO',
                'SPRINT' => 'SPRINT',
                "PROD" => "PROD",
                "DEV" => "DEV"
            ]),
            'list' => $this->faker->randomKey([
                'progress' => 'progress',
                'to do' => 'to do',
                'to be tested'=>'to be tested'
            ]),
            'member' => $this->faker->randomKey([
                'Davide'=>'Davide',
                'Alessio'=>'Alessio',
                'Pedram'=>'Pedram',
                'Marco'=>'Marco',
                'Gianmarco'=>'Gianmarco'
            ]),
        ];
    }
}
