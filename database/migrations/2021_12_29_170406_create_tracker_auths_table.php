<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackerAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracker_auths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('admin');
            $table->unsignedBigInteger('tracker_user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('tracker_user_id')->references('id')->on('tracker_users');
            $table->string('name');
            $table->json('auth')->nullable();
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
        Schema::dropIfExists('tracker_auths');
    }
}
