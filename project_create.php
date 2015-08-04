<?php
	require_once 'config.php';
        require_once 'lib/db/db.class.php';
	require_once 'lib/yandex.xml/Yandex.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Статистика позиций сайтов. Создание нового проекта.</title>
	<link rel="stylesheet" href="http://www.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<script src="http://www.onlysites.ru/assets/js/jquery-1.4.2.min.js" language="javascript" type="text/javascript"></script>
</head>
<body>
<div class="header_line"><a href="index.php">К списку проектов</a></div>
<?php
        $params = new Config();
        $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	$dbID->Query("SET NAMES UTF8");
	
?>
<div style="padding: 12px;">
<form name="project_create" method="post" action="project_create_save.php">
<table width="100%">
<tr>
<td valign="top" width="50%" align="left">
<h1>Свойства проекта</h1>
<br />
<table class="table_edit">
	<tr><td>Имя проекта:</td><td><input type="text" name="name" value="" /></td></tr>
	<tr><td>URL проекта:</td><td><input type="text" name="url" value="" /></td></tr>
	<tr><td>Регион проекта:</td><td><?php require_once 'regions.php'; ?></td></tr>
	<tr><td colspan="2" align="right"><input style="float: right; color: #FFF; background: red; border: 1px; cursor: hand; cursor: pointer;" type="submit" name="Создать проект" value="Создать проект" /></td></tr>
</table>	

</form>
<?php	
	$dbID->Destroy();
?>
</div>
</body>
</html>