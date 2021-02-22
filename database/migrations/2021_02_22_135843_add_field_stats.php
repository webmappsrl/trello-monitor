<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trello_customers', function (Blueprint $table) {
            $table->integer('all_days_worked_customer')->nullable();
            $table->integer('seven_days_worked_customer')->nullable();
            $table->integer('thirty_days_worked_customer')->nullable();
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
            $table->dropColumn('all_days_worked_customer');
            $table->dropColumn('seven_days_worked_customer');
            $table->dropColumn('thirty_days_worked_customer');
        });
    }
}
