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

class GenerateInvoiceJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
            $campaignService->generateInvoice();
        }catch(Exception $e){
            \Log::info("Invoice Job exception: ". ($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
        return;
    }
}
