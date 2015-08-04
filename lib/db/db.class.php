<?php 
class DataBaseMysql {
	var $dbId;
	function DataBaseMysql($host, $user, $password, $database) { if (!$this->dbId = @mysql_connect($host, $user, $password)) die("<b>MySQL</b>: Unable to connect to database"); if (!mysql_select_db($database)) die("<b>MySQL</b>: Unable to select database <b>".$database."</b>"); }
	function Query($sqlString) { if (!$resourseId =@mysql_query($sqlString, $this->dbId)) die("<b>MySQL</b>: Unable to execute<br /><b>SQL</b>: ".$sqlString."<br /><b>Error (".mysql_errno().")</b>: ".@mysql_error()); return $resourseId; }
	function SelectValue($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row =& mysql_fetch_row($resourseId); @mysql_free_result($resourseId); return $row[0]; }
	function &SelectRow($sqlString) { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); $row =& mysql_fetch_assoc($resourseId); @mysql_free_result($resourseId); return $row; }
	function &SelectSet($sqlString, $idTable = '') { $resourseId = DataBaseMysql::Query($sqlString); $row = array(); while ($rowOne =& mysql_fetch_assoc($resourseId)) { if ($idTable) $row[$rowOne[$idTable]] = $rowOne; else $row[] = $rowOne; } @mysql_free_result($resourseId); return $row; }
	function Destroy() { if (!@mysql_close($this->dbId)) die("Нет возможности отключиться от базы данных"); }
}
?>