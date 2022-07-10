<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->integer('location_id')->index();
            $table->integer('active');
            $table->integer('confirmed');
            $table->integer('deaths');
            $table->integer('recovered');
            $table->string('updated_stamp')->nullable();
            $table->integer('number_of_cases_last_twenty_eight_days')->nullable();
            $table->integer('number_of_deaths_last_twenty_eight_days')->nullable();
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
        Schema::dropIfExists('cases');
    }
}
