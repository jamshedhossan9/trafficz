<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\CampaignService;

class CampaignStatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaignId, $date)
    {
        $this->campaignId = $campaignId;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CampaignService $campaignService)
    {
        try{
            $campaignService->getSingleCampaignStats($this->campaignId, $this->date);
        }catch(Exception $e){
            \Log::info("getSingleCampaignStats Job exception: ". json_encode($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
    }
}
