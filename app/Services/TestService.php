<?php

namespace App\Services;


use App\Jobs\TestJob;
use App\Jobs\TestAfterJob;

use App\Models\MyLog;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Carbon\Carbon;
use Exception;

class TestService
{
    public function __construct()
    {
        // $this->date = date('Y-m-d',strtotime("-1 days"));
    }

    public function testRunJob(){
        $jobs = [];
        for($i = 1; $i <= 10; $i++){
            $jobs[] = new TestJob($i, date('Y-m-d H:i:s'));
            // TestJob::dispatch($i, date('Y-m-d H:i:s'))->delay(Carbon::now()->addSeconds(2));
        }
        $jobs[] = new TestAfterJob();
        // TestAfterJob::dispatch()->delay(Carbon::now()->addSeconds(20));
        $this->batchJob($jobs);
        echo 'Job ran successfully';
    }

    public function job($campaignId, $date){
        sleep(5);
        $data = new MyLog();
        $data->type = "test batch job";
        $data->data = [
            'date_current' => date('Y-m-d H:i:s'),
            'campaign_id' => $campaignId,
            // 'date' => $date,
            // 'source' => 'test job'
        ];
        $data->save();
    }

    public function afterJob(){
        sleep(10);
        $data = new MyLog();
        $data->type = "test batch job done";
        $data->data = [
            'source' => 'test job done'
        ];
        $data->save();
    }

    public function batchJob($jobs = []){
        if(!empty($jobs)){
            Bus::chain($jobs)->dispatch();
        }

        return;
    }

}