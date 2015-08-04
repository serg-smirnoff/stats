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
	
	$current_date = date('Y-m-d');
	
        $params = new Config();
        $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	$dbID->Query("SET NAMES UTF8");
	
	$dbID->Query("INSERT INTO projects VALUES ('','$data[name]','$data[url]','$current_date','','','','',$data[region],'1')");
	$dbID->Destroy();

	header('Location: http://stat.onlysites.ru/index.php');
?>
</body>
</html>