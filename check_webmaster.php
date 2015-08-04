<?php

$client_id = "1e14369dd1b74b50b0254bc2325f96c1";
$client_secret = "42f26d8e7cf740bbaddc0f1e950ac22b";

// Если мы еще не получили разрешения от пользователя, отправляем его на страницу для его получения
// В урл мы также можем вставить переменную state, которую можем использовать для собственных нужд, я не стал
if (!isset($_GET["code"])) {
    Header("Location: https://oauth.yandex.ru/authorize?response_type=code&client_id=".$client_id);
    die();
    }

// Если пользователь нажимает "Разрешить" на странице подтверждения, он приходит обратно к нам
// $_Get["code"] будет содержать код для получения токена. Код действителен в течении часа.
// Теперь у нас есть разрешение и его код, можем отправлять запрос на токен.

$result=postKeys("https://oauth.yandex.ru/token",
    array(
        'grant_type'=> 'authorization_code', // тип авторизации
        'code'=> $_GET["code"], // наш полученный код
        'client_id'=>$client_id,
        'client_secret'=>$client_secret
        ),
    array('Content-type: application/x-www-form-urlencoded')
    );

// отправляем запрос курлом

function postKeys($url,$peremen,$headers) {
    $post_arr=array();
    foreach ($peremen as $key=>$value) {
        $post_arr[]=$key."=".$value;
        }
    $data=implode('&',$post_arr);
    
    $handle=curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
    $response=curl_exec($handle);
    $code=curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return array("code"=>$code,"response"=>$response);
    }

// после получения ответа, проверяем на код 200, и если все хорошо, то у нас есть токен

	if ($result["code"]==200) {
		$result["response"]=json_decode($result["response"],true);
		$token=$result["response"]["access_token"];
	}else{
		echo "Какая-то фигня! Код: ".$result["code"];
	}

/*
   $token="наш полученный токен";
   функция, для курления
 */

function get_stat($url,$headers) {
    $handle=curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $response=curl_exec($handle);

	//var_dump(curl_getinfo ($handle));
	
    $code=curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return array("code"=>$code,"response"=>$response);
}

// при получении результатов, вы можете отслеживать код ответа по $result["code"]

// первый запрос - получение id пользователя по Яндексу.
// В ответ нам придет ссылка типа https://webmaster.yandex.ru/api/123456789, 123456789 - id пользователя
// Можете сохранить себе как сразу ссылку, так и id юзера отдельно

$result=get_stat('https://webmaster.yandex.ru/api/me',array('Authorization: OAuth '.$token));
var_dump($result);

$user_id=str_replace('https://webmaster.yandex.ru/api/','',$result["response"]);

// далее мы можем запросить сервисный документ, но в чем его смысл я так и не понял.
// возвращается ссылка вида: href="https://webmaster.yandex.ru/api/123456789/hosts, 123456789 - id пользователя
// поэтому получать его не будем, сразу запросим список сайтов

$result=get_stat('https://webmaster.yandex.ru/api/'.$user_id.'/hosts',array('Authorization: OAuth '.$token));
$xml=new SimpleXMLElement($result["response"]);
$hosts_xml=$xml->xpath("host");
$hosts=array();
foreach($hosts_xml as $host) {
    $hosts[(string)$host->name]=
        array(
            "name"=>(string)$host->name,
            "verification_state"=>(string)$host->verification->attributes()->state,
            "crawling_state"=>(string)$host->crawling->attributes()->state,
            "virused"=>(string)$host->virused,
            "last-access"=>(string)$host->{'last-access'},
            "tcy"=>(string)$host->tcy,
            "url-count"=>(string)$host->{'url-count'},
            "index-count"=>(string)$host->{'index-count'},
            "href"=>(string)$host->attributes()->href
            );
    }
unset($hosts_xml);
unset($xml);

/* в результате у нас имеется двумерный массив со всеми сайтами и частью их характеристик
ссылку для доступа к сайту также можно хранить целиком, для удобства в будущем
Array (
    [domen] => Array ( 
        [name] => domen - доменное имя
        [verification_state] => VERIFIED - статус подтверждения прав на управление доменом
        [crawling_state] => INDEXED - статус индексирования
        [virused] => false - наличие обнаруженных вирусов на сайте
        [last-access] => 2012-11-06T22:54:10 - последний доступ робота к сайту
        [tcy] => 150 - ТИЦ
        [url-count] => 7458 - количество забранных урлов
        [index-count] => 6131 - количество урлов в индексе
        [href] => https://webmaster.yandex.ru/api/id пользователя/hosts/id сайта - ссылка для доступа к статистике сайта
        )
    )
*/
// пробуем запросить полную статистику по сайту
// в ответе придет xml, которая уже содержит информацию, полученную до этого, так что берем только то, чего у нас нет

$site_href="https://webmaster.yandex.ru/api/654321/hosts/123456"; // 654321 - user_id, 123456 - site_id
$result=get_stat($site_href."/stats",array('Authorization: OAuth '.$token));
$xml=new SimpleXMLElement($result["response"]);
$errors=(string)$xml->{'url-errors'}; // количество страниц с ошибками
//$internal-links=(string)$xml->{'internal-links-count'}; // количество внутренних ссылок
$links=(string)$xml->{'links-count'}; // количество внешних входящих ссылок
unset($xml);	
	
?>