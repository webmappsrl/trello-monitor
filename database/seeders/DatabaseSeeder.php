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

            'name' => 'Pedram Katanchi',
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

            'name' => 'Gianmarco Gagliardi',
            'email' => 'gianmarcogagliardi@webmapp.it',
            'password' => bcrypt('webmapp2020'),
            'role' => 'developer'
        ]);

//        Storage::deleteDirectory('test_data');
//        Storage::makeDirectory('fixtures');
//
//        $tcc = new TrelloCardController();
////
//        foreach (TRELLO_BOARDS as $beardName => $boardId ) {
//            $cards = $tcc->_downloadCardsFromBoard($boardId);
//            Storage::put('test_data/cards.json', json_encode($cards));
//
//
//
//
//
//
//
//
//
//            foreach ($cards as $index => $card) {
//                Storage::put("test_data/card_{$index}.json", json_encode($cards[$index]));
//
//                $board = $tcc->_updateBoard($card->idBoard);
//                Storage::put("test_data/board_{$index}.json", json_encode($board));
//
//                $list = $tcc->_updateList($card->idList);
//                Storage::put("test_data/list_{$index}.json", json_encode($list));
//
//                if (is_array($card->idMembers) && count($card->idMembers) > 0)
//                {
//                    $member = $tcc->_updateMember($card->idMembers[0]);
//                    Storage::put("test_data/member_{$index}.json", json_encode($member));
//                }
//
//                $cf = $tcc->_downloadCard($card->id, 'customFieldItems');
//                Storage::put("test_data/cf_{$index}.json", json_encode($cf));
//
//                $estimate = $tcc->_downloadCard($card->id, 'pluginData');
//                Storage::put("test_data/estimate_{$index}.json", json_encode($estimate));
//
//                $total_time = $tcc->_downloadCard($card->id, 'actions');
//                Storage::put("test_data/total_time_{$index}.json", json_encode($total_time));
//
//            }
//        }


    }
}
