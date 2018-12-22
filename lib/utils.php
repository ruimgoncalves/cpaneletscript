<?php

function findInArrOfObj($arr, $value, $col='id', $comparer){
    if (!isset($comparer))
    $comparer = function($a, $b){
        return $a == $b;
    };

    foreach ($arr as $el) {
        if ($comparer($el->{$col}, $value) )
            return $el;
    }
    return NULL;
}

function daysToDate($endDate){
    $certEndDate = (new DateTime())->setTimestamp($endDate);
    $today = new DateTime("now");
    return $certEndDate->diff($today)->format("%a");
}

function aDef($arr, $key, $default = NULL){
    return isset($arr[$key]) ? $arr[$key] : $default;
}

function jsonOut($value)
{
    header('Content-Type: application/json');
    print json_encode($value);
    exit;
}