<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{

    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('event_id');
            $table->string('title');
            $table->timestamps();

            $table->foreign('event_id')
                ->references('id')->on('events')
                ->onDelete('cascade');
        });
    }
}