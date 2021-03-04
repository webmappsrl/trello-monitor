<?php

namespace Database\Seeders;

use App\ClassU\downloadCard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TrelloCardController;


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
            'name' => 'Alessio Piccioli',
            'email' => 'alessiopiccioli@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'admin'
        ]);

        \App\Models\User::factory(1)->create([
            'name' => 'pedramkat',
            'email' => 'pedramkatanchi@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

        \App\Models\User::factory(1)->create([
            'name' => 'Davide Pizzato',
            'email' => 'davidepizzato@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

        \App\Models\User::factory(1)->create([
            'name' => 'Antonella Puglia',
            'email' => 'antonellapuglia@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

        \App\Models\User::factory(1)->create([
            'name' => 'marcobarbieri70',
            'email' => 'marcobarbieri@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'cartographer'
        ]);
    }
}
