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
            $table->dateTime('last_activity');
            $table->text('name');
            $table->text('link');
            $table->integer('estimate');
            $table->integer('total_time');
            $table->boolean('is_archived')->default(False);

            $table->foreignId('customer_id')->nullable();
            $table->string('trello_id')->unique();
            $table->foreignId('list_id')->nullable();
            $table->foreignId('member_id')->nullable();

            $table->foreign('customer_id')
                ->references('id')
                ->on('trello_customers');
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
