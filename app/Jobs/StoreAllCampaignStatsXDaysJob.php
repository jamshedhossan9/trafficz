<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\CampaignService;

class StoreAllCampaignStatsXDaysJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 14000;

    protected $day;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($day)
    {
        $this->day = $day;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CampaignService $campaignService)
    {
        try{
            $campaignService->storeAllCampaignStatsXDays($this->day);
        }catch(Exception $e){
            \Log::info("storeAllCampaignStatsXDays Job exception: ". json_encode($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
        return;
    }
}
