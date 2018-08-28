<?php
        require_once 'lib/db/db.class.php';
	require_once 'lib/yandex.xml/Yandex.php';
?>
<?php
/*
	Проекты из таблицы projects пробиваем на ключи из таблицы keywords, результат пишем в positions с учетом даты пробивки.
*/
header ("Content-Type: text/html;charset=utf-8");

$params = new Config();
$dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
$dbID->Query("SET NAMES UTF8");
$projects = $dbID->SelectSet("SELECT * from projects WHERE is_active = 1");

$i = 0;

foreach ($projects as $key => $value){	
	$keys = $dbID->SelectSet("SELECT * from keywords WHERE project_id = '$value[id]' AND is_active = 1");
	$keys = $keys["0"];
	$projects[$i]["keys"] = $keys;
	$i++;
}

foreach ($projects as $key => $project) {

	$host = $project["url"];
	$host_esc  = htmlspecialchars($host);
	$host = preg_replace("[^http://|www\.]", '', $host);
	
	$phrase = $project["keys"]["phrase"];
	$phrases = explode(";", $phrase);
	$reg = $region = $project["region"];
	$reg_esc = htmlspecialchars($reg);

	$page	= 0;
	$pages	= 3;
	$error	= false;
	$dpd	= 0;

	$positions = "";
		
	foreach ($phrases as $key => $query_esc){
		$found = 0;
		$query_esc = str_replace(" ", "+", $query_esc);
		$query_esc = trim($query_esc);

		for ($page=0,$exit=false; $page<=$pages; $page++)
		{
			if ($exit) break;
			$response = file_get_contents('http://xmlsearch.yandex.ru/xmlsearch?user=programmatore&key=03.29915828:0e54bb50b2061a2a18038bb37ff306cb&text='.$query_esc.'&page='.$page.'&groupby=attr%3Dd.mode%3Ddeep.groups-on-page%3D10.docs-in-group%3D1&lr='.$reg);
                        if ( $response ) {    
					$xmldoc = new SimpleXMLElement($response);
					$xmlresponce = $xmldoc->response;
					if ($xmlresponce->error) {
						print "Ошибка: " . $xmlresponce->error . "<br/>\n";
						$error = true;
						break;
					}
					$pos = 1;
					$nodes = $xmldoc->xpath('/yandexsearch/response/results/grouping/group/doc/url');
					foreach ($nodes as $node) {
						if ( preg_match("/^http:\/\/(www\.)?$host/i", $node) ) {
							$found = $pos + $page * 10;
							//print_r ($node);
							$exit=true;
							break;
						}
						$pos++;
					}
				} else {
					print "Внутренняя ошибка сервера\n";
				}
		}
		$dpd+=$page;
		$query_esc = str_replace("+", " ", $query_esc);
		if (!$error) {
			if ($found) {
				$positions = $positions.$found.";";
			} elseif ($host) {
				$positions = $positions.">". $pages * 10 .";";
			} else {				
			
			}
		}
	}
	
	$current_date = date('Y-m-d');
	$dbID->Query("INSERT INTO positions VALUES ('','$project[id]', '$current_date', '$positions', '', '1')");
	
}

$dbID->Destroy();

?>