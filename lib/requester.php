<?php

// include_once '../vendor/autoload.php';
// Importing the classes.
use LEClient\LEClient;
use LEClient\LEOrder;

function requestCertificate($conf, $testing) {
    $client = new LEClient($conf['email'], $testing, LEClient::LOG_STATUS,
        "_certs/{$conf['domains'][0]}/"
        // "_accounts/{$conf['domains'][0]}/"
    );

    $order = $client->getOrCreateOrder($conf['domains'][0], $conf['domains']);
    if(!$order->allAuthorizationsValid()) {
        // Get the HTTP challenges from the pending authorizations.
        $pending = $order->getPendingAuthorizations(LEOrder::CHALLENGE_TYPE_HTTP);
            // Walk the list of pending authorization HTTP challenges.
            if(!empty($pending))
            {
                foreach($pending as $challenge)
                {
                    // Define the folder in which to store the challenge. For the purpose of this example, a fictitious path is set.
                    // $folder = $conf['publicPath'] . $challenge['identifier'] . '/.well-known/acme-challenge/';
                    $folder = $conf['publicPath'] . '/.well-known/acme-challenge/';
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
        if($order->isFinalized() && $order->getCertificate()) {
            return [
                'cert' => file_get_contents("_certs/{$conf['domains'][0]}/certificate.crt"),
                'privatekey' => file_get_contents("_certs/{$conf['domains'][0]}/private.pem")
            ];
        }
    }
    return null;
}