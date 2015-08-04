<?php
	require_once 'config.php';
        require_once 'lib/db/db.class.php';
	require_once 'lib/yandex.xml/Yandex.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Статистика позиций сайтов. Позиции сегодня.</title>
	<link rel="stylesheet" href="http://www.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/style.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/colorbox.css" type="text/css" />
	<link rel="stylesheet" href="http://stat.onlysites.ru/assets/css/themes/blue/style.css" type="text/css" media="print, projection, screen" />
	<link rel="icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="http://www.onlysites.ru/assets/images/faviconst.ico" type="image/x-icon" />
	<script src="http://www.onlysites.ru/assets/js/jquery-1.4.2.min.js" language="javascript" type="text/javascript"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="http://stat.onlysites.ru/assets/js/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="http://stat.onlysites.ru/assets/js/jquery.colorbox-min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
        $("#maintable").tablesorter();
		$(".inline").colorbox();
	});
	</script>
</head>
<body>
<?php
        
        $params = new Config();
        $dbID = new DataBaseMysql($params->host,$params->user,$params->pass,$params->base);
	$dbID->Query("SET NAMES UTF8");

	$projects = $dbID->SelectSet("SELECT * from projects WHERE id = '$_GET[project_id]' AND is_active = '1'");
		$project = $projects[0]["name"];
		$region = $projects[0]["region"];

	$keywords = $dbID->SelectSet("SELECT * from keywords WHERE project_id = '$_GET[project_id]' AND is_active = '1'");
		$keywords = $keywords[0];
		
	$current_date = date('Y-m-d'); //current_date
	$yesterday_date = date("Y-m-d", time() - 60 * 60 * 24); //yesterday_date
	
	$positions = $dbID->SelectSet("SELECT * from positions WHERE project_id = '$_GET[project_id]' AND is_active = '1' AND date = '$current_date'");
		$positions = $positions[0];

	$positions_2 = $dbID->SelectSet("SELECT * from positions WHERE project_id = '$_GET[project_id]' AND is_active = '1' AND date = '$yesterday_date'");
		$positions_2 = $positions_2[0];
	
	$keys = $keywords["phrase"];
	$positions_yandex = $positions["positions_yandex"];
	$positions_yandex_2 = $positions_2["positions_yandex"];
	$positions_google = $positions["positions_google"];

	$arr1 = explode(";",$keys); // keys_today
	$arr2 = explode(";",$positions_yandex); // positions_yandex
	$arr2_1 = explode(";",$positions_yandex_2); // positions_yandex_yesterday
	$arr3 = explode(";",$positions_google);
	
?>
<div class="header_line">
	<a href="index.php">К списку проектов</a>
	<div style="float: right;">Позиции по проекту <?php echo $project; ?> на <?php echo $current_date = date('Y-m-d'); ?></div>
	<div class="clear"></div>
</div>
<div style="padding: 12px;">
<br />
<?php
	$positions_all_date = $dbID->SelectSet("SELECT * from positions WHERE project_id = '$_GET[project_id]' AND is_active = '1'");
		foreach ($positions_all_date as $key => $pos){
			$res[$key]["date"] = $pos["date"];
			$keys = explode (";",$pos["positions_yandex"]);
			
			$top3 = 0; $top10 = 0; $top30 = 0;

			foreach ($keys as $keys_key => $value){
				if ($value <= '3') $top3++;
				if ($value <= '10') $top10++;
				if ($value <= '30') $top30++;
			}
			
			$res[$key]["top3"]  = $top3 - 1;
			$res[$key]["top10"] = $top10 - 1;
			$res[$key]["top30"] = $top30 - 1;
		}	
		function cmp($a, $b) {
			return strnatcmp($a["date"], $b["date"]);
		}
		usort($res, "cmp");
?>
<script type="text/javascript">
  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart);
  function drawChart() {
	var data = google.visualization.arrayToDataTable([
	  ['Year','Y-TOP3','Y-TOP10','Y-TOP30'],
	<?php
	foreach ($res as $r => $v){
		echo "['".$v['date']."',".$v['top3'].",".$v['top10'].",".$v['top30']."],";
	}
	?>  
	]);
	var options = {
	  title: 'График позиций'
	};
	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, options);
  }
</script>

<div id="chart_div" style="width: 100%; height: 300px; border: 1px solid #009383;"></div>

<br />

<table id="maintable" class="tablesorter" width="100%">
<thead> 
<tr>
	<th>N</th>
	<th>Ключевое слово</th>
	<th>Wordstat</th>
	<th>Yandex</th>
	<th>Google</th>
	<th>История позиции (график)</th>
</tr>
</thead> 
<?php $i=0; for ($i=0; $i<sizeof($arr1); $i++){?>
<tr>
	<td><?php echo $i+1; ?></td>
	<td><?php if($arr2[$i] != ">30") {?><strong style='color: #000;'><?php }?><?php echo $arr1[$i];?><?php if($arr2[$i] != ">30") {?></strong><?php }?></td>
	<td>
	<script type="text/javascript">
	function check_wordstat_<?php echo $i+1;?>(){
		var link = 'check_wordstat.php?region=<?php echo $region;?>&phrase=<?php echo $arr1[$i];?>';
		$.ajax({
		  url: link,
		  type: "GET",
	      beforeSend: function(){
	            $('#wordstat_<?php echo $i+1;?>').html('<img id="imgcode" src="assets/images/ajax-loader.gif" />');
		  },
		  success:function(data){
               $('#wordstat_<?php echo $i+1;?>').html(data);
          },
          error:function(){
				$('#wordstat_<?php echo $i+1;?>').html('error');
		  }
		});
	}
	</script>
	<div id="wordstat_<?php echo $i+1;?>">
		<span onclick="check_wordstat_<?php echo $i+1;?>();" style="text-decoration: underline; cursor: pointer; color: rgb(191, 57, 10);">check wordstat</span>
	</div>
	</td>
	<?php
		$delta_plus = $arr2[$i]-$arr2_1[$i];
		$delta_minus = $arr2_1[$i]-$arr2[$i];
	?>
	<td class='td_position'><?php if ($arr2[$i] == ">30") {echo "<div style='color: gray;'>".$arr2[$i]."</div>";}
			  elseif ($arr2[$i] > $arr2_1[$i]) {echo $arr2[$i]; echo "<span class='red'>+".$delta_plus."</span>";}
			  elseif ($arr2[$i] < $arr2_1[$i]) {echo $arr2[$i]; echo "<span class='green'>-".$delta_minus."</span>";} 
			  else {echo $arr2[$i];}
		?>
	</td>
	<td><?php echo "-"; //echo $arr3[$i];?></td>
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