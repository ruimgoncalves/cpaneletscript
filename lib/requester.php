<?php

// include_once '../vendor/autoload.php';
// Importing the classes.
use LEClient\LEClient;
use LEClient\LEOrder;

function requestCertificate($email, $accountConfig, $testing) {
    $client = new LEClient($email, $testing, LEClient::LOG_STATUS);
    $order = $client->getOrCreateOrder($accountConfig['domains'][0], $accountConfig['domains']);
    if(!$order->allAuthorizationsValid()) {
        // Get the HTTP challenges from the pending authorizations.
        $pending = $order->getPendingAuthorizations(LEOrder::CHALLENGE_TYPE_HTTP);
            // Walk the list of pending authorization HTTP challenges.
            if(!empty($pending))
            {
                foreach($pending as $challenge)
                {
                    // Define the folder in which to store the challenge. For the purpose of this example, a fictitious path is set.
                    // $folder = $accountConfig['publicPath'] . $challenge['identifier'] . '/.well-known/acme-challenge/';
                    $folder = $accountConfig['publicPath'] . '/.well-known/acme-challenge/';
                    // Check if that directory yet exists. If not, create it.
                    if(!file_exists($folder)) mkdir($folder, 0777, true);
                    // Store the challenge file for this domain.
                    file_put_contents($folder . $challenge['filename'], $challenge['content']);
                    // Let LetsEncrypt verify this challenge.
                    $order->verifyPendingOrderAuthorization($challenge['identifier'], LEOrder::CHALLENGE_TYPE_HTTP);
                }
        }
    }

    // Check once more whether all authorizations are valid before we can finalize the order.
    if($order->allAuthorizationsValid())
    {
        // Finalize the order first, if that is not yet done.
        if(!$order->isFinalized())
            $order->finalizeOrder();
        // Check whether the order has been finalized before we can get the certificate. If finalized, get the certificate.
        if($order->isFinalized())
            return $order->getCertificate();
    }
    return null;
}

/*
$config = require('../config.php');

foreach ($config['accounts'] as $email => $accConfig) {
    $cert = requestCertificate($email, $accConfig, true);
    echo $cert;
}
*/