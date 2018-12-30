<?php

if(!defined("PHP_VERSION_ID") || PHP_VERSION_ID < 50400 || !extension_loaded('openssl') || !extension_loaded('curl')) {
    die("You need at least PHP 5.4.0 with OpenSSL and curl extension\n");
}

ini_set('max_execution_time', 120);

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/lib/utils.php';
require __DIR__.'/lib/cpanel.php';
require __DIR__.'/lib/requester.php';

// Always use UTC
date_default_timezone_set("UTC");


if (php_sapi_name() == 'cli'){
    $opts = getopt('',['config:']);
    $configName = aDef($opts, 'config');
} else {
    $configName = aDef($_GET, 'config');
}

if (!isset($configName)){
    $config = require('config.php');
}
else{
    $cname = str_replace('/', '_', $configName);
    $cname = substr($cname, 0, 20);
    $config = require("config.{$cname}.php");
}

if (strlen($config['cpanel']['username']) == 0) {
    mlog( "Please edit the default configuration file or specify an alternative configuration.");
    return;
}

try {

    $cpanel = new \Cpanel\Cpanel([
        'host'        => $config['cpanel']['host'], // ip or domain complete with its protocol and port
        'username'    => $config['cpanel']['username'],
        'password'    => $config['cpanel']['password'],
    ]);

    $installedHosts = $cpanel->query('SSL', 'installed_hosts', [] );
    if (!isset($installedHosts)){
        mlog( "Cannot connect to Cpanel, check connection and configuration.");
        return;
    }
    $certsArr = $installedHosts->data;

    // mlog($certsArr);

    foreach ($config['accounts'] as $email => $domainData) {
        mlog( "Checking account {$email} -> {$domainData['domains'][0]}");

        $installedCert = findInArrOfObj($certsArr, $domainData['domains'][0], 'domains', function($a,$b){
            return in_array($b,$a);
        });

        if (isset($installedCert)) {
            $certEndDate = $installedCert->certificate->not_after;
            $daysLeft = daysToDate($certEndDate);
            $certEndDateFormated = (new DateTime())->setTimestamp($certEndDate)->format('d/m/y');
            if ($daysLeft > $config['minDays']){
                mlog( " {$installedCert->servername} expires {$certEndDateFormated} you have {$daysLeft} days left");
                mlog( "===========================================================");
                continue;
            } else {
                mlog( "  Expired needs to be updated!!!!!!!!");
            }

        } else {
            mlog("  There are no certificates for this domain requesting!");
        }
        continue;

        $cert = requestCertificate($email, $domainData, $config['testing']);
        // mlog($cert);
        if (is_array($cert)) {
            $certData = $cpanel->query('SSL', 'fetch_key_and_cabundle_for_certificate', ['certificate' => $cert['cert']] );

            mlog("Installing certificate");
            mlog($certData);
            $certInstall = $cpanel->query('SSL', 'install_ssl', [
                'domain'    => $certData->data->domain,
                'cert'      => $certData->data->crt,
                'key'       => $cert['privatekey'], // $certData->data->key,
                'cabundle'  => $certData->data->cab,
            ] );
            mlog($certInstall->data->statusmsg);
        } else {
            mlog("!!!!!Couldn't generate certificate!!!!!");
        }
        mlog( "===========================================================");
    }

} catch (\Exception $e) {
    mlog( "--== Exception ==--");
    mlog($e->getMessage());
    mlog($e->getTraceAsString());
}