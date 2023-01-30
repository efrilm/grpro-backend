<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dates', function (Blueprint $table) {
            $table->id();

            $table->string('lead_id');
            $table->dateTime('date_add');
            $table->dateTime('date_follow_up')->nullable();
            $table->dateTime('date_will_visit')->nullable();
            $table->dateTime('date_already_visit')->nullable();
            $table->dateTime('date_reservation')->nullable();
            $table->dateTime('date_booking')->nullable();
            $table->dateTime('date_sold')->nullable();
            $table->dateTime('date_refund')->nullable();


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dates');
    }
}
