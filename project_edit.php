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
	<?php
		if (isset($_GET["project_id"])) $project_id = $_GET["project_id"];
		
                $params = new Config();
                $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
		$dbID->Query("SET NAMES UTF8");
		
                $project = $dbID->SelectSet("SELECT * from projects WHERE is_active = 1 AND id = $project_id");
		$project = $project["0"];
		$keywords = $dbID->SelectSet("SELECT * from keywords WHERE is_active = 1 AND project_id = $project_id");
		if ((sizeof($keywords)) > 0) $keywords = $keywords["0"];
		
	?>
	<?php $current_reg_id = $project['region']; ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#<?php echo $current_reg_id; ?>').attr('selected', true);
	});
	</script>
</head>
<body>
<div class="header_line"><a href="index.php">К списку проектов</a></div>
<div style="padding: 12px;">
<form name="edit_form" method="post" action="projects_edit_save.php">
<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id" />
<input type="hidden" value="<?php echo $keywords['phrase']; ?>" name="phrases" />
<table width="100%">
<tr>
<td valign="top" width="50%" align="left">
<h1>Свойства проекта</h1>
<br />
<table class="table_edit">
	<tr><td>Имя проекта:</td><td><input type="text" name="name" value="<?php echo $project['name']; ?>" /></td></tr>
	<tr><td>URL проекта:</td><td><input type="text" name="url" value="<?php echo $project['url']; ?>" /></td></tr>
	<tr><td>Регион проекта:</td><td><?php require_once 'regions.php'; ?></td></tr>
	<tr><td>Текущее значение региона</td><td><input type="text" disabled="disabled" name="region_num" value="<?php echo $project['region']; ?>" /></td></tr>
	<tr><td>PR:</td><td><input type="text" disabled="disabled" name="pr" value="<?php echo $project['pr']; ?>" /></td></tr>
	<tr><td>ТИЦ:</td><td><input type="text" disabled="disabled" name="tic" value="<?php echo $project['tic']; ?>" /></td></tr>
	<tr><td>Страниц в Yandex:</td><td><input type="text" disabled="disabled" name="pages_yandex" value="<?php echo $project['pages_in_yandex']; ?>" /></td></tr>
	<tr><td>Страниц в Google:</td><td><input type="text" disabled="disabled" name="pages_google" value="<?php echo $project['pages_in_google']; ?>" /></td></tr>
</table>	
</td>
<td valign="top" width="50%" align="right">
<h1>Ключевые слова</h1>
<br />
<div class="table_edit"><textarea name="keys" value="<?php foreach ($keys as $k => $v) echo $v."\r\n"; ?>"><?php if ((sizeof($keywords)) > 0) $keys = explode(";",$keywords["phrase"]); else $keys = "";
	$i=1;
	if ((sizeof($keywords)) > 0)
		foreach ($keys as $k => $v){
			echo $v; if ($i<>sizeof($keys)) echo "\r\n"; 
			$i++;
		} else
			echo "";
?></textarea></div>
<br />
<div style="width:445px;"><input style="float: right; color: #FFF; background: red; border: 1px; cursor: hand; cursor: pointer;" type="submit" name="Изменить" value="Изменить" /></div>
<br />
</td></tr></table>
</form>
<?php	
	$dbID->Destroy();
?>
</div>
</body>
</html>