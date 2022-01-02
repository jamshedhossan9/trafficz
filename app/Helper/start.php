<?php

use Illuminate\Support\Facades\Route;

function isSuperAdmin(){
    $roleId = auth()->user()->roles->first()->pivot->role_id;
    return $roleId == 1;
}

function isAdmin(){
    $roleId = auth()->user()->roles->first()->pivot->role_id;
    return $roleId == 2;
}

function isUser(){
    return !isSuperAdmin() && !isAdmin();
}

function defaultRoute(){
    if(isSuperAdmin()){
        return route('superAdmin.users.index');
    }
    return '/home';
}

function version(){
    return '?v='.microtime();
}

if (!function_exists('user')) {
    function user()
    {
        $user = auth()->user();

        return $user;
    }
}

/* menuActive
 * @author Jamshed Hossan
 * @param mixed
 * @param string (optional)
 * @return string
 * 
 * Description with Usage:
 * set class to menu, default class 'active'
 * 
 * menuActive($routes, $prefix); $routes = array|string, $prefix = string(optional)
 * -------------
 * menuActive('prefix.route-1');
 * menuActive('route-1', 'prefix');
 * menuActive(['prefix.route-1', 'prefix.route-2', 'prefix.route-3']);
 * menuActive(['route-1', 'route-2', 'route-3'], 'prefix');
 * 
 * 
 * menuActive($params); $params = array : routes(array|string), class(string, optional, default: active), prefix(string, optional)
 * ------------------ use this cases specially if you need to pass class name
 * menuActive(['route' => 'prefix.route-1', 'class' => 'open']);
 * menuActive(['route' => ['prefix.route-1', 'prefix.route-2', 'prefix.route-3'], 'class' => 'open']);
 * menuActive(['route' => ['route-1', 'route-2', 'route-3'], 'prefix' => 'prefix', 'class' => 'open']);
*/
function menuActive($params, $prefix = '')
{ 
    $class = '';
    $defaults = [
        'prefix' => '',
        'routes' => [],
        'class' => 'active',
    ];
    $currentRoute = Route::currentRouteName();
    if(is_string($params)){
        $defaults['routes'] = [$params];
        $defaults['prefix'] = trim($prefix);
    }
    else{
        if(array_keys($params) !== range(0, count($params) - 1)){
            if(!empty($params['class'])){
                $tempClass = trim($params['class']);
                if($tempClass != ''){
                    $defaults['class'] = $tempClass;
                }
            }
            if(!empty($params['prefix'])){
                $defaults['prefix'] = trim($params['prefix']);
            }
            if(!empty($params['route'])){
                if(is_string($params['route']))
                    $defaults['routes'] = [$params['route']];
                else
                    $defaults['routes'] = $params['route'];
            }
        }
        else{
            $defaults['prefix'] = trim($prefix);
            $defaults['routes'] = $params;
        }
    }
    if($defaults['prefix'] != ''){
        foreach($defaults['routes'] as $key => $item){
            $defaults['routes'][$key] = $defaults['prefix'].'.'.$item;
        }
    }

    if(in_array($currentRoute, $defaults['routes'])){
        $class = $defaults['class'];
    }

    return $class;
}

function extractHeaderAndBody($params = [])
{
    $ch = $params['ch'];
    $response = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    return [
        'body' => trim($body),
        'bodyArray' => json_decode(trim($body), true),
        'header' => trim($header),
        'status' => $status
    ];
}

function trackerCampaignStatDefaults(){
    $output = [
        'conversions' => 0,
        'cost' => 0,
        'revenue' => 0,
        'profit' => 0,
        'impressions' => 0,
        'visits' => 0,
        'clicks' => 0,
        'epc' => 0,
        'cpc' => 0,
        'epv' => 0,
        'cpv' => 0,
        'roi' => 0,
        'ctr' => 0,
        'ictr' => 0,
        'cr' => 0,
    ];

    return $output;
}

function trackerCampaignStatMerge($data, $tracker){
    $output = trackerCampaignStatDefaults();
    if($tracker == "voluum"){
        $output['conversions'] = empty($data['conversions']) ? 0 : floatval($data['conversions']);
        $output['cost'] = empty($data['cost']) ? 0 : floatval($data['cost']);
        $output['revenue'] = empty($data['revenue']) ? 0 : floatval($data['revenue']);
        $output['profit'] = empty($data['profit']) ? 0 : floatval($data['profit']);
        $output['impressions'] = empty($data['impressions']) ? 0 : floatval($data['impressions']);
        $output['visits'] = empty($data['visits']) ? 0 : floatval($data['visits']);
        $output['clicks'] = empty($data['clicks']) ? 0 : floatval($data['clicks']);
        $output['epc'] = empty($data['epc']) ? 0 : floatval($data['epc']);
        // $output['cpc'] = empty($data['cpc']) ? 0 : floatval($data['cpc']);
        $output['epv'] = empty($data['epv']) ? 0 : floatval($data['epv']);
        $output['cpv'] = empty($data['cpv']) ? 0 : floatval($data['cpv']);
        $output['roi'] = empty($data['roi']) ? 0 : floatval($data['roi']);
        $output['ctr'] = empty($data['ctr']) ? 0 : floatval($data['ctr']);
        $output['ictr'] = empty($data['ictr']) ? 0 : floatval($data['ictr']);
        $output['cr'] = empty($data['cr']) ? 0 : floatval($data['cr']);

        $output['cpc'] = $output['clicks'] == 0 ? 0 : $output['cost'] / $output['clicks'];
    }
    else if($tracker == "binom"){
        $output['conversions'] = empty($data['leads']) ? 0 : floatval($data['leads']);
        $output['cost'] = empty($data['cost']) ? 0 : floatval($data['cost']);
        $output['revenue'] = empty($data['revenue']) ? 0 : floatval($data['revenue']);
        $output['profit'] = empty($data['profit']) ? 0 : floatval($data['profit']);
        $output['impressions'] = empty($data['impressions']) ? 0 : floatval($data['impressions']);
        $output['visits'] = empty($data['clicks']) ? 0 : floatval($data['clicks']);
        $output['clicks'] = empty($data['lp_clicks']) ? 0 : floatval($data['lp_clicks']);
        $output['epc'] = empty($data['lp_epc']) ? 0 : floatval($data['lp_epc']);
        // $output['cpc'] = empty($data['cpc']) ? 0 : floatval($data['cpc']);
        $output['epv'] = empty($data['epc']) ? 0 : floatval($data['epc']);
        // $output['cpv'] = empty($data['cpv']) ? 0 : floatval($data['cpv']);
        $output['roi'] = empty($data['roi']) ? 0 : floatval($data['roi']);
        $output['ctr'] = empty($data['ctr']) ? 0 : floatval($data['ctr']);
        // $output['ictr'] = empty($data['ictr']) ? 0 : floatval($data['ictr']);
        $output['cr'] = empty($data['cr']) ? 0 : floatval($data['cr']);

        $output['cpc'] = $output['clicks'] == 0 ? 0 : $output['cost'] / $output['clicks'];
        $output['cpv'] = $output['visits'] == 0 ? 0 : $output['cost'] / $output['visits'];
    }

    return $output;
}

function trackerCampaignStatSum($data = []){
    $output = trackerCampaignStatDefaults();
    foreach($data as $item){
        foreach ($item as $key2 => $value2) {
            if(isset($output[$key2])){
                $output[$key2] += floatval($value2);
            }
        }
    }

    $output['epc'] = $output['clicks'] ==  0 ? 0 : $output['revenue']/$output['clicks'];
    $output['epv'] = $output['visits'] ==  0 ? 0 : $output['revenue']/$output['visits'];
    $output['cpv'] = $output['visits'] ==  0 ? 0 : $output['cost']/$output['visits'];
    $output['cpc'] = $output['clicks'] ==  0 ? 0 : $output['cost']/$output['clicks'];
    $output['cr'] = $output['clicks'] == 0 ? 0 : ($output['conversions']/$output['clicks']) * 100;
    $output['ctr'] = $output['visits'] ==  0 ? 0 : ($output['clicks']/$output['visits']) * 100;
    $output['ictr'] = $output['impressions'] ==  0 ? 0 : ($output['visits']/$output['impressions']) * 100;
    $output['roi'] = $output['cost'] ==  0 ? 0 : ($output['profit']/$output['cost']) * 100;
    
    return $output;
}

function createVoluumSession($keyId, $key, $withLoginInfo = false)
{
    $postData = [
        'accessId' => $keyId,
        'accessKey' => $key,
    ];
    $url = 'https://api.voluum.com/auth/access/session';
    if($withLoginInfo){
        $postData = [
            'email' => $keyId,
            'password' => $key,
        ];
        $url = 'https://api.voluum.com/auth/session';
    }

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_POST, 1);

    $headers = array();
    $headers[] = "Content-Type: application/json; charset=utf-8";
    $headers[] = "Accept: application/json";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $result = json_decode($result, true);
    // var_dump($result);
    if (curl_errno($ch)) {
    	return false;
    }

    curl_close($ch);
    if (isset($result['error']))
        return false;
    else {
        return $result['token'];
    }
}

function getVoluumReport($auth, $query, $limit = 1000, $offset = 0)
{
    $token = createVoluumSession($auth['access_key_id'], $auth['access_key'], true);
    if($token){
        $ch = curl_init();

        $query .= '&offset='.$offset.'&limit='.$limit;
        curl_setopt($ch, CURLOPT_URL, "https://api.voluum.com/report?".$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $headers = array();
        $headers[] = "Cache-Control: no-cache";
        $headers[] = "Cwauth-Token: " . $token . "";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = extractHeaderAndBody(['ch' => $ch]);

        //print_r($result);

        curl_close($ch);
        return !empty($result['bodyArray']) ? $result['bodyArray'] : false;
    }
    return false;
}

function getVoluumCampaignStat($auth, $from, $to, $id){
    $stats = [
        "advertiserCost" => 0,
        "clicks" => 0,
        "conversions" => 0,
        "cost" => 0,
        "cpv" => 0,
        "cr" => 0,
        "ctr" => 0,
        "cv" => 0,
        "ecpa" => 0,
        "ecpc" => 0,
        "ecpm" => 0,
        "epc" => 0,
        "epv" => 0,
        "ictr" => 0,
        "impressions" => 0,
        "profit" => 0,
        "revenue" => 0,
        "roi" => 0,
        "rpm" => 0,
        "uniqueClicks" => 0,
        "uniqueVisits" => 90,
        "visits" => 125,
    ];
    $offset = 0;
    $limit = 1000;
    $dates = [
        'fromDateOnly' => $from,
        'toDateOnly' => date("Y-m-d", strtotime("+1 day", strtotime($to))),
    ];
    $campaignId = $id;
    $query = 'from='.$dates['fromDateOnly'].'T00:00:00Z&to='.$dates['toDateOnly'].'T00:00:00Z';
    $query .= '&tz=America%2FNew_York&currency=USD&columns=created,campaignId,campaignNamePostfix,flowName,campaignUrl,impressions,visits,clicks,conversions,revenue,cost,ictr,ctr,roi&include=ACTIVE&groupBy=campaign&sort=created&direction=ASC&filter1=campaign&filter1Value='.$campaignId;
    $result = getVoluumReport($auth, $query);
    if($result && !empty($result['totals'])){
        $stats = $result['totals'];
    }
    return $stats;
}

function getVoluumCampaignStatByHour($auth, $from, $to, $id){
    $offset = 0;
    $limit = 1000;
    $dates = [
        'fromDateOnly' => $from,
        'toDateOnly' => date("Y-m-d", strtotime("+1 day", strtotime($to))),
    ];
    $campaignId = $id;
    $query = 'from='.$dates['fromDateOnly'].'T00:00:00Z&to='.$dates['toDateOnly'].'T00:00:00Z';
    $query .= '&tz=America%2FNew_York&currency=USD&columns=created,hourOfDay,campaignId,campaignNamePostfix,flowName,campaignUrl,impressions,visits,clicks,conversions,revenue,cost,ictr,ctr,roi&include=ACTIVE&groupBy=campaign&groupBy=hour-of-day&sort=created&direction=ASC&filter1=campaign&filter1Value='.$campaignId;
    return getVoluumReport($auth, $query);
}

function getBinomCampaignStat($auth, $from, $to, $id){
    $stats = [
        "clicks" => 0,
        "bots" => 0,
        "lp_clicks" => 0,
        "lp_views" => 0,
        "leads" => 0,
        "cost" => 0,
        "revenue" => 0,
        "unique_clicks" => 0,
        "unique_camp_clicks" => 0,
        "ea" => 100,
        "lp_ctr" => 0,
        "cr" => 0,
        "epc" => 0,
        "cpc" => 0,
        "profit" => 0,
        "roi" => 0
    ];
    $postData = [
        'page' => 'Stats',
        'date' => 12,
        'date_s' => $from,
        'date_e' => $to,
        'group1' => 39,
        'camp_id' => $id,
        'timezone' => '-5:00',
        'api_key' => $auth['api_key'],
    ];
    $statUrl = $auth['web_portal_url'].'?'.(http_build_query($postData, '', '&', PHP_QUERY_RFC3986));

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $statUrl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
      ),
    ));

    $response = curl_exec($curl);
    if(curl_errno($curl)){

    }
    else{
        $response = json_decode($response, true);
        $status = empty($response['status']) ? 'success' : $response['status'];
        if(is_array($response) && !empty($response) && $status == 'success'){
            foreach ($response as $key => $value) {
                if($value['level'] == 1){
                    foreach ($value as $key2 => $value2) {
                        if(isset($stats[$key2])){
                            $stats[$key2] += floatval($value2);
                        }
                    }
                }
            }
        }
    }
    $stats['cpv'] = ($stats['clicks'] == 0) ? 0 : $stats['cost']/$stats['clicks'];

    return $stats;
}

function getBinomCampaignStatByHour($auth, $from, $to, $id){
    $postData = [
        'page' => 'Stats',
        'date' => 12,
        'date_s' => $from,
        'date_e' => $to,
        'group1' => 26,
        'camp_id' => $id,
        'timezone' => '-5:00',
        'api_key' => $auth['api_key'],
    ];
    $statUrl = $auth['web_portal_url'].'?'.(http_build_query($postData, '', '&', PHP_QUERY_RFC3986));

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $statUrl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
      ),
    ));

    $response = curl_exec($curl);
    if(curl_errno($curl)){

    }
    else{
        $response = json_decode($response, true);
        $status = empty($response['status']) ? 'success' : $response['status'];
        if(is_array($response) && !empty($response) && $status == 'success'){
            return $response;
        }
    }
    return false;
}

function getTrackerCampaignStat($tracker, $auth, $from, $to, $id){
    if($tracker == 'voluum'){
        $result = getVoluumCampaignStat($auth, $from, $to, $id);
        $result = trackerCampaignStatMerge($result, $tracker);
    }
    else if($tracker == 'binom'){
        $result = getBinomCampaignStat($auth, $from, $to, $id);
        $result = trackerCampaignStatMerge($result, $tracker);
    }
    else{
        $result = trackerCampaignStatDefaults();
    }
    return $result;
}