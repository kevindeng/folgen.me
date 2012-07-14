<?php require_once('../Connections/folgen.php'); ?>
<?php include('../includes/getvaluestr.php'); ?>
<?php
$date3 = $_POST['subtask-deadline'];
$parsed_date4 = explode("/", $date4);
$date_to_send4 = "$parsed_date4[2]-$parsed_date4[0]-$parsed_date4[1]";
$subtask_insertSQL = sprintf("INSERT INTO task (tid, id, title, `description`, deadline) VALUES (%s, %s, %s, %s)",
				   GetSQLValueString($tid, "text"),
				   GetSQLValueString('', "text"),
				   GetSQLValueString($_POST['subtask-title'], "text"),
				   GetSQLValueString($_POST['task-complete'], "int"),
				   GetSQLValueString($date_to_send4, "date"));
mysql_select_db($database_folgen, $folgen);
$Result_subtask = mysql_query($subtask_insertSQL, $folgen) or die(mysql_error());



echo "success";
?>