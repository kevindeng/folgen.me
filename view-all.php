<?php require_once('Connections/folgen.php'); ?>
<?php require('includes/getvaluestr.php'); ?>
<?php require_once('includes/restrict-access.php'); ?>

<?php

$colname_show_tasks = "-1";

mysql_select_db($database_folgen, $folgen);
$query_getUserId = sprintf("SELECT id FROM user WHERE email = %s", GetSQLValueString($_SESSION['MM_Username'], "text"));
$result = mysql_query($query_getUserId, $folgen) or die(mysql_error());
$userid = mysql_fetch_array($result);
$myid = $userid['id'];

//show projects
$query_show_projects = sprintf("SELECT id, title, `description`, deadline FROM project WHERE `uid` = %s ORDER BY deadline ASC", GetSQLValueString($myid , "int"));
$show_projects = mysql_query($query_show_projects, $folgen) or die(mysql_error());
$row_show_projects = mysql_fetch_assoc($show_projects);
$totalRows_show_projects = mysql_num_rows($show_projects);

?>

<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Create a project</title>
<link href="css/master.css" rel="stylesheet" type="text/css">
<link href='http://fonts.googleapis.com/css?family=Fredoka+One' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="scripts/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/3.5.1/build/yui/yui-min.js"></script>
<script type="text/javascript" src="http://yui.yahooapis.com/3.5.1/build/substitute/substitute.js"></script>
<script type="text/javascript" src="tasks.js"></script>

</head>

<body>
<?php include('includes/header.php') ?>

     <?php if ($totalRows_show_projects > 0) { // Show if recordset not empty ?>
     <table>
            <?php do { ?>
            <tr>
              <td><?php echo $row_show_projects['title']; ?> <a href="developer-view.php?project_id=<?php echo $row_show_projects['id']; ?>  ">view</a></td>
            </tr>
            <?php } while ($row_show_projects = mysql_fetch_assoc($show_projects)); ?>
    </table>
    <?php } ?>

<?php include('includes/footer.php') ?>
</body>
</html>