<?php
	require_once("config.php");
	require_once("lib/db/db.class.php");
	require_once("lib/yandex.xml/Yandex.php");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Статистика позиций сайтов. Проекты</title>
	<link rel="stylesheet" href="http://www.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<script src="http://www.onlysites.ru/assets/js/jquery-1.4.2.min.js" language="javascript" type="text/javascript"></script>
</head>
<body>
<?php
        
	$params = new Config();
	$dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	
	//$dbID->Query("SET NAMES UTF8");
	$dbID->Query("USE stat");
	$res = $dbID->SelectSet("SELECT * from projects WHERE is_active = 1");

	/* include external php scripts */
			
	include_once ("project_get_tic.php");
	include_once ("project_get_pr.php");
	include_once ("project_pages_in_google.php");
	include_once ("project_pages_in_yandex.php");
	
	echo "<table width='100%' id='maintable'>";
	echo "<tr>
			<th>id</th>
			<th>Проект</th>
			<th>Запросы (шт)</th>
			<th>Рост ↑ (шт)</th>
			<th>Падение ↓ (шт)</th>
			<th>ТИЦ</th>
			<th>PR</th>
			<th>индекс Yandex</th>
			<th>индекс Google (основной)</th>
			<th>индекс Google (supplemental)</th>
			<th>Статистика</th>	
			<th>Редактировать</th>
		</tr>";
	
	foreach ($res as $key => $value){

	$keys = $dbID->SelectValue("SELECT phrase from keywords WHERE is_active = 1 AND project_id = $value[id]");

	$keys = explode (";",$keys);		
	$num_keys = sizeof($keys); 
	
	$num_keys_up = 0;
	$num_keys_down = 0;	
	
	echo "<tr>
			<td>".$value["id"]."</td>
                        <td><a href='project_view.php?project_id=".$value["id"]."'>".$value["name"]."</a></td>
			<td>".$num_keys."</td>
			<td>".$num_keys_up."</td>
			<td>".$num_keys_down."</td>
			<td style='color: #BF390A;'>".get_yandex($value["name"])."</td>
			<td style='color: #BF390A;'>".GetPageRank($value["name"])."</td>
			<td style='color: #BF390A;'><div style='white-space: nowrap; width: 50px; overflow: hidden;'>".YandexPagesXml($value["name"])."</div></td>
			<td style='color: #BF390A;'>".GoogleIndex($value["name"])."</td>
			<td>-</td>
			<td><a href='project_edit.php?project_id=".$value["id"]."' style='text-align: center;'><img src='assets/images/view.png' width='20' height='20' alt='' /></a><a href='project_view.php?project_id=".$value["id"]."' style='float: left;'>смотреть</a><div class='clear: both;'></div></td>
			<td><a href='project_edit.php?project_id=".$value["id"]."' style='text-align: center;'><img src='assets/images/edit.png' width='20' height='20' alt='' /></a><a href='project_edit.php?project_id=".$value["id"]."' style='float: left;'>редактировать</a><div class='clear: both;'></div></td>
		</tr>";
	}
	
	echo "</table>";
?>

<br />

<div style="border: 1px solid #009383; padding: 21px;">
    <div style="height: 40px; line-height: 40px;"><a href="project_create.php"><img src="assets/images/add.png" width="20" height="20" alt="" style="padding-right: 20px;" /> добавить проект</a></div>
    <div style="height: 40px; line-height: 40px;"><a href="check_positions.php"><img src="assets/images/start.png" width="20" height="20" alt="" style="padding-right: 20px;" /> запустить пробивку</a> [по сегодняшней дате. запускается по cron ежедневно в 02:00]</div>
</div>

<?php 
function getContent($url, $agent = false){
   $contentPage = '';
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_TIMEOUT, 30);
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
   curl_setopt($ch, CURLOPT_URL, $url);
   $contentPage = curl_exec($ch);
   curl_close($ch);
   return trim($contentPage);
}?>

<br />

<div style="border: 1px solid #009383; padding: 21px;"><?php 

	function win_utf8($string){
		$out = '';
		for ($i = 0; $i<strlen($string); ++$i){
		$ch = ord($string{$i});
		if ($ch < 0x80) $out .= chr($ch);
		else
		if ($ch >= 0xC0)
		if ($ch < 0xF0)
		$out .= "\xD0".chr(0x90 + $ch - 0xC0); // А-Я, а-п (A-YA, a-p)
		else $out .= "\xD1".chr(0x80 + $ch - 0xF0); // р-я (r-ya)
		else
		switch($ch){
		case 0xA8: $out .= "\xD0\x81"; break; // YO
		case 0xB8: $out .= "\xD1\x91"; break; // yo
		// ukrainian
		case 0xA1: $out .= "\xD0\x8E"; break; // Ў (U)
		case 0xA2: $out .= "\xD1\x9E"; break; // ў (u)
		case 0xAA: $out .= "\xD0\x84"; break; // Є (e)
		case 0xAF: $out .= "\xD0\x87"; break; // Ї (I..)
		case 0xB2: $out .= "\xD0\x86"; break; // I (I)
		case 0xB3: $out .= "\xD1\x96"; break; // i (i)
		case 0xBA: $out .= "\xD1\x94"; break; // є (e)
		case 0xBF: $out .= "\xD1\x97"; break; // ї (i..)
		// chuvashian
		case 0x8C: $out .= "\xD3\x90"; break; // Ӑ (A)
		case 0x8D: $out .= "\xD3\x96"; break; // Ӗ (E)
		case 0x8E: $out .= "\xD2\xAA"; break; // Ҫ (SCH)
		case 0x8F: $out .= "\xD3\xB2"; break; // Ӳ (U)
		case 0x9C: $out .= "\xD3\x91"; break; // ӑ (a)
		case 0x9D: $out .= "\xD3\x97"; break; // ӗ (e)
		case 0x9E: $out .= "\xD2\xAB"; break; // ҫ (sch)
		case 0x9F: $out .= "\xD3\xB3"; break; // ӳ (u)
		}
		}
		return $out;
	} 

	/*
	$content = getContent("http://tools.promosite.ru/");
	
	$start_rule = "<td valign=\"top\" swidth=\"80%\" class=text>";
	$stop_rule = "<td valign=\"top\" width=\"1\" class=rightmenu>";
	
	$rule = "!".$start_rule."(.*?)".$stop_rule."!si";
	preg_match($rule,$content,$content_match);
		
	$ret = win_utf8($content_match["0"]);
	$ret = str_replace("href=\"/", "href=\"http://tools.promosite.ru/", $ret);
	$ret = str_replace("src=\"/", "src=\"http://tools.promosite.ru/", $ret);

	echo $ret;
	*/
	
?></div>

<br />

<h1>Некоторые полезные сервисы</h1>

<div style="border: 1px solid #009383; padding: 21px;">
 
 <a style="padding-right: 12px; float: left;" href="http://wordstat.yandex.ru/advq?rpt=ppc&shw=1" target="_blank">Статистика поисковых запросов по Яндексу</a>
 <a style="padding-right: 12px; float: left;" href="http://www.yandex.ru/cgi-bin/test-robots" target="_blank">Файл robots.txt глазами Яндекса</a>
 <a style="padding-right: 12px; float: left;" href="http://www.copyscape.com/" target="_blank">Проверка контента на уникальность</a>
 <a style="padding-right: 12px; float: left;" href="http://web.archive.org/web/" target="_blank">Веб архив</a>
 <a style="padding-right: 12px; float: left;" href="http://www.be1.ru/vface/ns.php" target="_blank">Оценка тошнотности страницы</a> 
 <a style="padding-right: 12px; float: left;" href="http://vface.controlstyle.ru/" target="_blank">Расчет стоимости ссылки со страницы</a>
 <a style="padding-right: 12px; float: left;" href="http://expire.ru-monitor.ru/" target="_blank">Мониторинг освобождающихся доменов</a>
 <a style="padding-right: 12px; float: left;" href="http://direct.yandex.ru/registered/main.pl?cmd=ForecastByWords" target="_blank">Расчет рекламного бюджета в Яндекс.Директ</a>
 <a style="padding-right: 12px; float: left;" href="http://www.miralab.ru/tools/service/">Все SEO сервисы</a>
 
 <br /><br /><br />
 
 <a style="padding-right: 12px; float: left;" href="http://webmaster.yandex.ru/sites/">Яндекс Вебмастер</a> 
 <a style="padding-right: 12px; float: left;" href="https://www.google.com/webmasters/tools/home?hl=ru">Google Webmaster</a> 
 
 <div style="clear: both;"></div>

</div>

<br />


<?php
	$dbID->Destroy();
?>
</body>
</html>