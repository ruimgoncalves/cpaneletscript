<?php

function mlog($data, $function = '')
{
    $e = new Exception();
    $trace = $e->getTrace();
    if (count($trace) < 3) {
        $function = '---';
    } else {
        $function = $function == '' ? 'function ' .  $trace[3]['function'] . ' (function ' . $trace[2]['function'] . ')' : $function;
    }

    if (PHP_SAPI == "cli")
    {
        echo '[' . date('d-m-Y H:i:s') . '] ' . $function . ":\n";
        print_r($data);
        echo "\n\n";
    }
    else
    {
        echo '<b>' . date('d-m-Y H:i:s') . ', ' . $function . ':</b><br>';
        print_r($data);
        echo '<br><br>';
    }
}

function getVal(&$value, $default = null)
{
    return isset($value) ? $value : $default;
}

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

function jsonOut($value)
{
    header('Content-Type: application/json');
    print json_encode($value);
    exit;
}