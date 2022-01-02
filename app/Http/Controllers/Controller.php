<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use stdClass;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            
            $this->pageTitle = '';
            $this->pageIcon = '';
            $this->user = user();

            return $next($request);
        });
    }

    public function ajaxRes($show = false){
        $output = new stdClass();
        $output->status = false;
        $output->msg = new stdClass();
        $output->msg->show = $show;
        $output->msg->title = 'Failed';
        $output->msg->text = '';
        $output->msg->type = 'error';
        $output->data = [];
        return $output;
    }
}
