<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignGroupReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_group_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_group_id');
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_group_id')->references('id')->on('campaign_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade')->onUpdate('cascade');
            $table->date('date');
            $table->bigInteger('conversions')->default(0);
            $table->double('cost')->default(0);
            $table->double('revenue')->default(0);
            $table->double('profit')->default(0);
            $table->bigInteger('impressions')->default(0);
            $table->bigInteger('visits')->default(0);
            $table->bigInteger('clicks')->default(0);
            $table->float('epc')->default(0);
            $table->float('cpc')->default(0);
            $table->float('epv')->default(0);
            $table->float('cpv')->default(0);
            $table->float('roi')->default(0);
            $table->float('ctr')->default(0);
            $table->float('ictr')->default(0);
            $table->float('cr')->default(0);

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
        Schema::dropIfExists('campaign_group_reports');
    }
}
