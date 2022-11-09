<?php

include('./config/config.php');
include('./config/global.php');

ini_set('memory_limit', '1500M');
ini_set('max_execution_time', 900);

/**
 * Monitoring for my portfolio
 * @author: @phi_and_code
 * frequency: 15min
 * description: Script that allows to query the server of my portfolio with a cURL and sends me an sms.
 */

$start = microtime(true);
$debug = true;

$url = $config['url'];
$num = $config['destinataire'];

function testUrl(string $url, string $destinataire)
{
    $timeout = 10;

    $curl = curl_init();

    if (!$curl) {
        die("cURL handle it's not initialize -- l.24");
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_HEADER, [
        "Cache-Control: no-cache, no-store, must-revalidate",
        "Pragma: no-cache"
    ]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $http_response = curl_exec($curl);

    $http_code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);

    if (curl_errno($curl)) {
        echo 'cURL Error : ' . curl_error($curl) . "<br>";
    } else {
        print_r($http_code, $url);
    }

    if (($http_code == '200') || ($http_code == '302')) {
        echo "The server is healthy";
        print_r($http_code);
    } else {
        echo "The server is down";
        print_r($http_code, "isn't good!");
        //SMS
        $sms = new Sms();
        $sms->sendAlerteMessage($destinataire, $http_code);
    }

    curl_close($curl);
}

testUrl($url, $num);

$end = microtime(true);

$testTime = ($end - $start);

if ($debug) {
    echo "<hr>";
    print_r($testTime . "ms");
}
