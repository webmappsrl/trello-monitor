<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgressTimeToTrelloCards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trello_cards', function (Blueprint $table) {
            $table->dateTime('last_progress_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trello_cards', function (Blueprint $table) {
            $table->dropColumn('last_progress_date');
        });
    }
}
