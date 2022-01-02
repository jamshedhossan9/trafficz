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