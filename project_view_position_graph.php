<?php
	require_once 'config.php';
	require_once 'lib/db/db.class.php';
	require_once 'lib/yandex.xml/Yandex.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Статистика позиций сайтов. Проект <?php echo $project_name; ?>. Позиции сегодня.</title>
	<link rel="stylesheet" href="http://www.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<script src="http://www.onlysites.ru/assets/js/jquery-1.4.2.min.js" language="javascript" type="text/javascript"></script>
	<!-- API -->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
  </head>    
</html>	
	
</head>
<body>
<div class="header_line"><a href="projects.php">К списку проектов</a></div>
<div style="padding: 12px;">
<?php
        $params = new Config();
        $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	$dbID->Query("SET NAMES UTF8");

	$keywords = $dbID->SelectSet("SELECT * from keywords WHERE project_id = '$_GET[project_id]' AND is_active = '1'");
		$keywords = $keywords[0];

	$current_date = date('Y-m-d');
	
	$positions = $dbID->SelectSet("SELECT * from positions WHERE project_id = '$_GET[project_id]' AND is_active = '1' AND date = '$current_date'");
		$positions = $positions[0];
	
	$keys = $keywords["phrase"];
	$positions_yandex = $positions["positions_yandex"];
	$positions_google = $positions["positions_google"];

	$arr1 = explode(";",$keys);
	$arr2 = explode(";",$positions_yandex);
	$arr3 = explode(";",$positions_google);
	
?>
<br />
<h1>Позиции по проекту на сегодня</h1>

<br />

<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = google.visualization.arrayToDataTable([
	  ['Year', 'Yandex', 'Google'],
	  ['2004',  1000,      400],
	  ['2005',  1170,      460],
	  ['2006',  660,       1120],
	  ['2007',  1030,      540]
	]);
	var options = {
	  title: 'График позиций'
	};
	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, options);
  }
</script>

<div id="chart_div" style="width: 681px; height: 300px;"></div>

<br />

<table class='maintable'>
<tr>
	<td>N</td>
	<td>Ключевое слово</td>
	<td>Wordstat</td>
	<td>Yandex</td>
	<td>Google</td>
	<td>История позиции (график)</td>
</tr>
<?php $i=0; for ($i=0; $i<sizeof($arr1); $i++){?>
<tr>
	<td><?php echo $i+1; ?></td>
	<td><?php echo $arr1[$i];?></td>
	<td><a href="#">проверить</a></td>
	<td><?php echo $arr2[$i];?></td>
	<td><?php echo "0"; //echo $arr3[$i];?></td>
	<td><a href="#">смотреть</a></td>
</tr>
<?php }?>
</table>

<br />

<div><a href="#" title="экспорт в xls">экспорт в xls</a></div>

<br />

<?php
	$dbID->Destroy();
?>

</div>
</body>
</html>