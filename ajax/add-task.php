<?php require_once('../Connections/folgen.php'); ?>
<?php include('../includes/getvaluestr.php'); ?>
<?php
$date3 = $_POST['task-deadline'];
$parsed_date3 = explode("/", $date3);
$date_to_send3 = "$parsed_date3[2]-$parsed_date3[0]-$parsed_date3[1]";
$task_insertSQL = sprintf("INSERT INTO task (id, title, `description`, deadline) VALUES (%s, %s, %s, %s)",
				   GetSQLValueString('', "text"),
				   GetSQLValueString($_POST['task-title'], "text"),
				   GetSQLValueString($_POST['task-description'], "text"),
				   GetSQLValueString($date_to_send3, "date"));
mysql_select_db($database_folgen, $folgen);
$Result_task = mysql_query($task_insertSQL, $folgen) or die(mysql_error());

$new_task_id = mysql_insert_id();
$assoc_sql = sprintf("INSERT INTO task_to_project VALUES (NULL, %d, %d, NULL)",
				GetSQLValueString($new_task_id, "int"),
				GetSQLValueString($_POST['project_id']));
mysql_query($assoc_sql);

echo "success";
?>