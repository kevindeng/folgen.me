<?php require_once('Connections/folgen.php'); ?>
<?php include('includes/getvaluestr.php'); ?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['email'])) {
  $loginUsername=$_POST['email'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "view-all.php";
  $MM_redirectLoginFailed = "fail.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_folgen, $folgen);
  
  $LoginRS__query=sprintf("SELECT email, password FROM `user` WHERE email=%s AND password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $folgen) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8">
<title>Keep track your projects progress</title>
<link href='http://fonts.googleapis.com/css?family=Fredoka+One' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href="css/master.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

</head>

<div id="intro-wrapper">
	<div id="intro">
    <h1>folgen.me</h1>
            <h2>Simply create and watch the progress of anything and everything</h2>
    	<!--intro page -->
    	<div id="page-intro" class="page">
            <br /><br />
            <div id="button-follow-project" class="classic-big-button">follow a project</div>
            <div id="button-create-project" class="classic-big-button">create a project</div>
         </div><!--end of intro-page
         
     <!--login screen / create a project-->
     <div id="page-login" class="page">
     	To create a project you must login :) <br /><br />
	   <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" id="form-page-login">
        <table width="200" align="center" border="0">
          <tr>
            <td align="right">Login:</td>
            <td> <input type="text" class="form-login" autocomplete="off" name="email"></td>
          </tr>
          <tr>
            <td align="right">Password:</td>
            <td><input type="password" class="form-login" autocomplete="off" name="password"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="login" ></td>
          </tr>
        </table>       
        </form>
     </div>
     
     <!--follow screen / follow a project-->
     <div id="page-follow" class="page">
        	Start following your project's progress <br /><br />
	   <form id="form-page-follow">
        <table width="200" align="center" border="0">
          <tr>
            <td align="right">Project ID:</td>
            <td> <input type="text" class="form-login" autocomplete="off" name="project_id"></td>
          </tr>
          <tr>
            <td align="right"> Project Password:</td>
            <td><input type="password" class="form-login" autocomplete="off" name="password"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="button" value="View Progress"></td>
          </tr>
        </table>       
        </form>
    </div><!--end of intro -->
</div><!--end of intro-wrapper -->

<script type="text/javascript">
$('#button-create-project').live('click', function(){
	console.log("create!!!");
	$('#page-intro').animate({
		opacity: 0.2,
		marginLeft: '-=10000'
	  }, 1000, function() {
		// Animation complete.
	  });
	$('.page').hide();
	//$('#page-login').show();
	 $('#page-login').animate({
		opacity: 1,
		left: '+=50',
		height: 'toggle'
	  }, 300, function() {
		// Animation complete.
	  });

});

$('#button-follow-project').live('click', function(){
	console.log("create!!!");
	$('#page-intro').animate({
		opacity: 0.2,
		marginLeft: '-=10000'
	  }, 1000, function() {
		// Animation complete.
	  });
	$('.page').hide();
	//$('#page-login').show();
	 $('#page-follow').animate({
		opacity: 1,
		left: '+=50',
		height: 'toggle'
	  }, 300, function() {
		// Animation complete.
	  });

});
</script>
<body>
</body>
</html>
