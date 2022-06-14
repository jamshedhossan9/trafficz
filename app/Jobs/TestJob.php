<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Services\TestService;

class TestJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    protected $campaignId;
    protected $date;
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
    public function handle(TestService $testService)
    {
        try{
            $testService->job($this->campaignId, $this->date);
        }catch(Exception $e){
            \Log::info("getSingleCampaignStats Job exception: ". json_encode($e->getMessage()));
            //release the job to try again after 10s
            $this->release(10);
        }
        return;
    }
}
