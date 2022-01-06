<?php

namespace App\Jobs;

use App\Services\CampaignService;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampaignGroupStatJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $groupId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CampaignService $campaignService)
    {
        try{
            $campaignService->getAllCampaignGroupStats($this->groupId);
        }catch(Exception $e){
            \Log::info("getAllCampaignGroupStats Job exception: ". json_encode($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
    }
}
