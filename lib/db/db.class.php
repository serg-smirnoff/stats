<?php 

class DataBaseMysql {
	
	var $dbID;
	
	function __construct($host, $user, $password, $database) { if (!$this->dbID = @mysqli_connect($host, $user, $password)) die("<b>MySQL</b>: Нет возможности подключиться к базе данных"); if (!mysqli_select_db($this->dbID, $database)) die("<b>MySQL</b>: Unable to select database <b>".$database."</b>"); }
	
	function Query($sqlString) { if (!$resourseId =@mysqli_query($this->dbID, $sqlString)) die("<b>MySQL</b>: Нет возможности выполнить<br /><b>SQL</b>: ".$sqlString."<br /><b>Error (".mysqli_errno($this->dbID).")</b>: ".@mysqli_error()); return $resourseId; }
	function SelectValue($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row = mysqli_fetch_row($resourseId); @mysqli_free_result($resourseId); return $row[0]; }
	function SelectRow($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row = mysqli_fetch_assoc($resourseId); @mysqli_free_result($resourseId); return $row; }
	function SelectSet($sqlString, $idTable = '') { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); while ($rowOne = mysqli_fetch_assoc($resourseId)) { if ($idTable) $row[$rowOne[$idTable]] = $rowOne; else $row[] = $rowOne; } @mysqli_free_result($resourseId); return $row; }
	
	function __destruct() { if (!@mysqli_close($this->dbID)) die("Нет возможности отключиться от базы данных"); }
	
}

?>