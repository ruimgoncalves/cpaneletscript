<?php

if(!defined("PHP_VERSION_ID") || PHP_VERSION_ID < 50400 || !extension_loaded('openssl') || !extension_loaded('curl')) {
    die("You need at least PHP 5.4.0 with OpenSSL and curl extension\n");
}

function findArrObj($arr, $value, $col='id'){
    foreach ($arr as $el) {
        if ($el->{$col} == $value)
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

require 'Lescript.php';
require 'cpanel.php';

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

$logger = aDef($config, 'logger');

// Always use UTC
date_default_timezone_set("UTC");

if (strlen($config['cpanel']['username']) == 0){
    $logger->error("Please edit the default configuration file or specify an alternative configuration.");
    return;
}

try {

    $cpanel = new \Cpanel\Cpanel([
        'host'        => $config['cpanel']['host'], // ip or domain complete with its protocol and port
        'username'    => $config['cpanel']['username'],
        'password'    => $config['cpanel']['password'],
    ]);

    $serverResp = $cpanel->query('SSL', 'installed_hosts', [] );
    if (!isset($serverResp)){
        $logger->error("Cannot connect to Cpanel, check connection and configuration.");
        return;
    }
    $certsData = $serverResp->data;

    foreach ($config['domains'] as $domain => $domainData) {
        $logger->info("Checking {$domain}");
        $installedCert = findArrObj($certsData, $domain, 'servername');
        if (isset($installedCert)){
            $logger->info("Found already installed cert for {$installedCert->servername}");
            $certEndDate = $installedCert->certificate->not_after;
            $daysLeft = daysToDate($certEndDate);
            $certEndDateFormated = (new DateTime())->setTimestamp($certEndDate)->format('d/m/y');
            if ($daysLeft > $config['minDays']){
                $logger->info("Expires {$certEndDateFormated} you have {$daysLeft} days left");
                continue;
            } else {
                $logger->info("Expired needs to be updated!!!!!!!!");
            }

        } else {
            $logger->info("There are no certificates for this domain requesting!");
        }

        $le = new Analogic\ACME\Lescript($config['storagePath'], $domainData['publicPath'], $logger);
        # or without logger:
        #$le = new Analogic\ACME\Lescript($config['storagePath'], $path);

        $le->countryCode =  $domainData['certInfo']['countryCode'];
        $le->state =        $domainData['certInfo']['state'];
        $le->contact =      $domainData['certInfo']['contact']; // optional

        $le->initAccount();
        $le->signDomains(array_merge([$domain], (array)aDef($domainData, 'and')));

        $certContent = file_get_contents($config['storagePath'] . '/' . $domain . '/cert.pem');
        $privateKey =  file_get_contents($config['storagePath'] . '/' . $domain . '/private.pem');
        $certData = $cpanel->query('SSL', 'fetch_key_and_cabundle_for_certificate', ['certificate' => $certContent] );

        $logger->info("Installing cert for domain {$domain}");
        $certInstall = $cpanel->query('SSL', 'install_ssl', [
            'domain'    => $certData->data->domain,
            'cert'      => $certData->data->crt,
            'key'       => $privateKey, //$certData->data->key,
            'cabundle'  => $certData->data->cab,
        ] );
        $logger->info($certInstall->data->statusmsg);
    }

} catch (\Exception $e) {
    $logger->error($e->getMessage(), $e->getTraceAsString());
}

