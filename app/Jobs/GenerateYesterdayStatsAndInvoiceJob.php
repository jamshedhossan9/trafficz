<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\CampaignService;

class GenerateYesterdayStatsAndInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CampaignService $campaignService)
    {
        try{
            $campaignService->generateYesterdayStatsAndInvoice();
        }catch(Exception $e){
            \Log::info("generateYesterdayStatsAndInvoice Job exception: ". json_encode($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
        return;
    }
}
