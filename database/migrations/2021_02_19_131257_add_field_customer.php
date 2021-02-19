<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trello_customers', function (Blueprint $table) {
            $table->dateTime('last_activity_progress')->nullable();
            $table->integer('cards')->nullable();
            $table->integer('todo')->nullable();
            $table->integer('done')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trello_customers', function (Blueprint $table) {
            $table->dropColumn('last_activity_progress');
            $table->dropColumn('cards');
            $table->dropColumn('todo');
            $table->dropColumn('done');
        });
    }
}
