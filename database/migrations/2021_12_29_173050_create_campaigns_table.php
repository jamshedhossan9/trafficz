<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_group_id');
            $table->unsignedBigInteger('tracker_auth_id');
            $table->foreign('campaign_group_id')->references('id')->on('campaign_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tracker_auth_id')->references('id')->on('tracker_auths')->onDelete('cascade')->onUpdate('cascade');
            $table->string('camp_id')->comment('Campaign id from tracker');
            $table->string('name')->comment('Alternate campaign name from admin');
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
        Schema::dropIfExists('campaigns');
    }
}
