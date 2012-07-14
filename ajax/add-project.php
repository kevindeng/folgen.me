<?php require_once('../Connections/folgen.php'); ?>
<?php include('../includes/getvaluestr.php'); ?>
<?php include('../includes/restrict-access.php');?>
<?php
mysql_select_db($database_folgen, $folgen);
$query_getUserId = sprintf("SELECT id FROM user WHERE email = %s", GetSQLValueString($_SESSION['MM_Username'], "text"));
$result = mysql_query($query_getUserId, $folgen) or die(mysql_error());
$userid = mysql_fetch_array($result);
$myid = $userid['id'];

$date = $_POST['project-deadline'];
$parsed_date = explode("/", $date);
$date_to_send = "$parsed_date[2]-$parsed_date[0]-$parsed_date[1]";

$date2 = $_POST['project-start'];
$parsed_date2 = explode("/", $date2);
$start_date_to_send = "$parsed_date2[2]-$parsed_date2[0]-$parsed_date2[1]";

$insertSQL = sprintf("INSERT INTO project (uid, id, title, `description`, start, deadline) VALUES (%s, %s, %s, %s, %s, %s)",
				   GetSQLValueString($myid, "int"),
				   GetSQLValueString('', "int"),
				   GetSQLValueString($_POST['project-title'], "text"),
				   GetSQLValueString($_POST['project-description'], "text"),
				   GetSQLValueString($start_date_to_send, "date"),
				   GetSQLValueString($date_to_send, "date"));

mysql_select_db($database_folgen, $folgen);
$Result1 = mysql_query($insertSQL, $folgen) or die(mysql_error());

$id = mysql_insert_id();
echo "success:".$id;
?>