<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code');
            $table->string('name');
            $table->string('no_whatsapp');
            $table->string('address')->nullable();
            $table->string('note');
            $table->string('source');
            $table->integer('sales_id');
            $table->integer('created_by');
            $table->integer('home_id')->nullable();
            $table->date('day');
            $table->enum('status', ['PENDING','NEW', 'FOLLOW UP', 'VISIT', 'RESERVATION', 'BOOKING', 'SOLD', 'REFUND'])->default('NEW');
            $table->enum('payment_method', ['HARD CASH', 'SOFT CASH', 'KPR'])->nullable();
            $table->string('project_code');
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
        Schema::dropIfExists('leads');
    }
}
