<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('admin');
            $table->unsignedBigInteger('tracker_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tracker_id')->references('id')->on('trackers');
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
        Schema::dropIfExists('tracker_users');
    }
}
