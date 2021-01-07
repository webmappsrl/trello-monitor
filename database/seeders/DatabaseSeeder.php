<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(1)->create([

            'name'=>'Alessio Piccioli',
            'email'=>'alessiopiccioli@webmapp.it',
            'password'=>bcrypt('webmapp2020'),
            'role' => 'admin'
        ]);

        \App\Models\User::factory(1)->create([

            'name'=>'Pedram Katanchi',
            'email'=>'pedramkatanchi@webmapp.it',
            'password'=>bcrypt('webmapp2020'),
            'role' => 'developer'

        ]);

        \App\Models\User::factory(1)->create([

            'name'=>'Davide Pizzato',
            'email'=>'davidepizzato@webmapp.it',
            'password'=>bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

        \App\Models\User::factory(1)->create([

            'name'=>'Gianmarco Gagliardi',
            'email'=>'gianmarcogagliardi@webmapp.it',
            'password'=>bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

        Storage::makeDirectory('test_data');
        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $this->_downloadCardsFromBoard($boardId);
            Storage::put('test_data/cards.json',json_encode($cards));
        }


    }
}
