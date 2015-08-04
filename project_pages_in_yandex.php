<?php
function YandexPagesXml($url)
{
$res_ = 0;

$url = str_replace("www.","",$url);
$wurl = "www.".$url;
 
$query = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<request>
<query>site:$url | site:$wurl</query>
<groupings>
<groupby attr="d" mode="deep" groups-on-page="10" docs-in-group="1"></groupby>
</groupings>
</request>
XML;
 
$xmlurl = 'http://xmlsearch.yandex.ru/xmlsearch?user=programmatore&key=03.29915828:0e54bb50b2061a2a18038bb37ff306cb&query=site:'.$url;
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL,$xmlurl);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,60); 
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_ENCODING,"gzip,deflate");
curl_setopt($curl,CURLOPT_POST,true);
curl_setopt($curl,CURLOPT_POSTFIELDS,$query);
$str = curl_exec($curl);
curl_close($curl);
preg_match('/Нашлось(.+?)ответов/',$str, $a);

$res_ = !empty($a[1]) ? $a[1] : 0;
return $res_;
}
?>