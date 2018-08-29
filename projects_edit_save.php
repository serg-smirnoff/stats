<?php
	require_once 'config.php';
	require_once 'lib/db/db.class.php';
	require_once 'lib/yandex.xml/Yandex.php';
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
<body style="padding: 12px;">
<?php
	if (isset($_POST)) $data = $_POST;
        $params = new Config();
        $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	$dbID->Query("SET NAMES UTF8");
	
	$project_id = $dbID->SelectSet("SELECT project_id from keywords WHERE is_active = 1 AND project_id = '$data[project_id]'");
	if (sizeof($project_id) > 0){
		
		$dbID->Query("UPDATE projects SET name = '$data[name]' WHERE is_active = '1' AND id = '$data[project_id]'");
		$dbID->Query("UPDATE projects SET url = '$data[url]' WHERE is_active = '1' AND id = '$data[project_id]'");
		$dbID->Query("UPDATE projects SET region = '$data[region]' WHERE is_active = '1' AND id = '$data[project_id]'");
		
		$str = $data["keys"];
		$keys = str_replace("\r\n", ";", $str);

		$dbID->Query("UPDATE keywords SET phrase = '$keys' WHERE is_active = '1' AND project_id = '$data[project_id]'");
	
	} else {
		
		$str = $data["keys"];
		$keys = str_replace("\r\n", ";", $str);

		$dbID->Query("INSERT INTO keywords VALUES ('','$data[project_id]', '2013-07-01', '$keys', '1')");
	
	}

	header('Location: https://stat.onlysites.ru/index.php');
	
?>
</body>
</html>