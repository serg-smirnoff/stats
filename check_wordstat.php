<?php

$region = ($_GET["region"] ? $_GET["region"] : $_GET["region"]);
$phrase = ($_GET["phrase"] ? $_GET["phrase"] : $_GET["phrase"]);

$local_cert = 'lib/wordstat/solid-cert.crt';
$wsdlurl = 'https://api.direct.yandex.ru/wsdl/v4/';

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($wsdlurl,
    array(
        'trace'=> 1,
        'exceptions' => 0,
        'encoding' => 'UTF-8',
        'local_cert' => $local_cert,
        'passphrase' => ''
    )
);

$params = array(
	"Phrases" => array($phrase),
	"GeoID" => array($region)
);

function getWordstatReport($client,$params){
	$reportID = $client->CreateNewWordstatReport($params);
	sleep(9);
	return $client->getWordstatReport($reportID);	
}

$wordstat = getWordstatReport($client,$params);
echo $wordstat[0]->SearchedWith[0]->Shows; 

?>