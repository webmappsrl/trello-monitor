<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrelloCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trello_cards', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->text('link');
            $table->text('customer')->nullable();;
            $table->integer('estimate')->nullable();;
            $table->integer('total_time')->nullable();;
            $table->boolean('is_archived')->default(False);


//            $table->dateTime('date_last_activity');
            $table->string('trello_id')->unique();
//            $table->foreignId('board_id')->nullable();
            $table->foreignId('list_id')->nullable();
            $table->foreignId('member_id')->nullable();

//            $table->foreign('board_id')
//                ->references('id')
//                ->on('trello_boards');
            $table->foreign('list_id')
                ->references('id')
                ->on('trello_lists');
            $table->foreign('member_id')
                ->references('id')
                ->on('trello_members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trello_cards');
    }
}
