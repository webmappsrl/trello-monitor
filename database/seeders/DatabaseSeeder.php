<?php

namespace Database\Seeders;

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

        Storage::deleteDirectory('test_data');
        Storage::makeDirectory('test_data');

        $tcc = new TrelloCardController();

        foreach (TRELLO_BOARDS as $beardName => $boardId) {
            $cards = $tcc->_downloadCardsFromBoard($boardId);
            Storage::put('test_data/cards.json', json_encode($cards));

            foreach ($cards as $card) {

                $board = $tcc->_updateBoard($card->idBoard);
                Storage::put("test_data/board_'{$card->idBoard}.'.json", json_encode($board));

                $list = $tcc->_updateList($card->idList);
                Storage::put("test_data/list_'{$card->idList}'.json", json_encode($list));

                if (is_array($card->idMembers) && count($card->idMembers) > 0)
                {
                    $member = $tcc->_updateMember($card->idMembers[0]);
                    Storage::put("test_data/member_'{$card->idMembers[0]}'.json", json_encode($member));
                }

                $cf = $tcc->_downloadCard($card->id, 'customFieldItems');
                Storage::put("test_data/cf_'{$card->id}'.json", json_encode($cf));

                $estimate = $tcc->_downloadCard($card->id, 'pluginData');
                Storage::put("test_data/estimate_'{$card->id}'.json", json_encode($estimate));

                $total_time = $tcc->_downloadCard($card->id, 'actions');
                Storage::put("test_data/total_time_'{$card->id}'.json", json_encode($total_time));

            }
        }
    }
}
