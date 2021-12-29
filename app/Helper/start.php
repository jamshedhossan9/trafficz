<?php

function isSuperAdmin(){
    $roleId = auth()->user()->roles->first()->pivot->role_id;
    return $roleId == 1;
}

function isAdmin(){
    $roleId = auth()->user()->roles->first()->pivot->role_id;
    return $roleId == 2;
}