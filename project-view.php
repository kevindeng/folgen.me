<?php require_once('Connections/folgen.php'); ?>
<?php require('includes/getvaluestr.php'); ?>

<?php
  mysql_select_db($database_folgen, $folgen);
  $query_getUserId = sprintf("SELECT id FROM user WHERE email = %s", GetSQLValueString($_SESSION['MM_Username'], "text"));
  $result = mysql_query($query_getUserId, $folgen) or die(mysql_error());
  $userid = mysql_fetch_array($result);
  $myid = $userid['id'];
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

<script type="text/javascript">
    function getURLParameter(name) {
        return decodeURI(
            (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
        );
    }

    $(function() {
      folgen.Project.loadInto(parseInt(getURLParameter('project_id'), 10), $('#main'));
    });
</script>

</head>

<body>
<?php include('includes/header2.php') ?>
  <?php echo "<input type=\"hidden\" id=\"__uid\" value=\"$myid\"></input>";?>

  <div id="main">
      <div id="task-controls">
        <span class="create-task button-controls">create a task</span>
        </div><!--end of task controls -->
         
    </div><!-- end of main -->
<?php include('includes/footer.php') ?>

<script type="text/javascript">
$( "#task-datepicker" ).datepicker();
$( "#subtask-datepicker" ).datepicker();

    function addTask(){
      var dat = $('#form-add-task').serialize();
      var project_id = parseInt(getURLParameter('project_id'), 10);
      dat += '&project_id=' + project_id;
      $.ajax({
          type:'POST', 
          url: 'ajax/add-task.php', 
          data:dat, 
          success: function(response) {
            if(response=="success"){
              $('#form-add-task-result').html("Task added with success!");
              $('#form-add-task-wrapper').slideUp('fast', function(){
                //animation complete
                window.location.replace("../developer-view.php?project_id=" + project_id);
              });
            }else{
              $('#form-add-task-result').html("There was an error adding this task");
            }
                
            }
        });
          return false;
      }
	  
	   function addSubtask(){
      var dat = $('#form-add-subtask').serialize();
      var project_id = parseInt(getURLParameter('project_id'), 10);
      dat += '&project_id=' + project_id;
      $.ajax({
          type:'POST', 
          url: 'ajax/add-subtask.php', 
          data:dat, 
          success: function(response) {
            if(response=="success"){
              $('#form-add-subtask-result').html("Subtask added with success!");
              $('#form-add-subtask-wrapper').slideUp('fast', function(){
                //animation complete
                window.location.replace("../developer-view.php?project_id=" + project_id);
              });
            }else{
              $('#form-add-subtask-result').html("There was an error adding this task");
            }
                
            }
        });
          return false;
      }
      

  $('#create-task-submit').click(function(){
    addTask();
  });

  $('.create-task').click(function() {
	   $('.popup').hide();
    $('#popup-create-new-task').fadeIn();
  });
  
 $('.create-subtask').click(function() {
	  $('.popup').hide();
    $('.popup-create-new-subtask').fadeIn();
  });
  
   $('.create-subtask-submit').click(function(){
    addSubtask();
  });

</script>

<!-- templates -->

<script type="text/template" id="task-template">
  <div id="tasks">
    <table width="100%"border="0" cellspacing="0">
      <tr>
        <td colspan="3"><span class="task-title">{title}</span> | <span class="task-deadline">Deadline: {deadline}</span> <br />
        <span class="label">{description}</span><br />
        <br />
        <span class="create-subtask button-controls" id="task-id">create subtask</span>
        </td>
      </tr>
      <tr>
        <td class="task-subtasks"></td>
      </tr>
      <tr>
        <td class="task-comments"></td>
      </tr>
      <tr>
        <td class="task-comment-add">
          <div><textarea cols="35"></textarea></div>
          <div><button>Comment</button></div>
        </td>
      </tr>
    </table>
  </div>
</script>

<script type="text/template" id="project-template">
  <div class="project">
    <span class="label">Progress Project Report For:</span>
    <h1>{title}</h1>
    <div id="progress-report" class="box">
      Progress: <span class="highlight">{progress}% completed</span> <br />
        Expenses: <span class="highlight">$120.00 has been spent so far</span> <br />
        Time Remaining: <span class="highlight">{remaining}</span> <br />
        Budget: <span class="highlight">$3,000.00</span> <br />
        Deadline: <span class="highlight">{deadline}</span>
        
    </div><!--end of progress report -->
    
    <div id="progress-bar-wrapper">
      <div id="progress-bar-today-tracker">
          <div class="progress-date-label start">start date: <br>{start}</div>
            <div class="progress-date-label deadline">deadline: <br>{deadline}</div>
          
          <div id="arrow-tracker"></div>
        </div><!--end of progress-bar-today-tracker -->
        <div id="progress-bar">
          <!--<div class="progress-task"> refactoring</div>-->
          <div id="progress-bar-fill"></div>
        </div><!-- end of progress-bar -->
        <div id="progress-bar-amount-wrapper">
          <div id="progress-bar-amount">
            <span class="highlight">$</span>120.00 <span class="label2"> spent</span>
            </div>
        </div><!--end of progress-bar-amount-wrapper-->
       
    </div><!--end of progress-bar-wrapper -->

    <div class="project-tasks"></div>
  </div>
</script>

<script type="text/template" id="comment-template">
  <div class="comment-wrap">
    <div class="comment-profile-pic">
      <img src="{imgSrc}"></img>
    </div>
    <div class="comment-text">
      <span class="comment-user-name">{user}</span>
      <span class="comment-content">{text}</span>
      <br />
      <span class="comment-time">{time}</span>
    </div>
  </div>
</script>

<script type="text/template" id="subtask-template">
  <div class="subtask-wrap">
    <div class="subtask-text">
      <span class="subtask-heading">Subtask</span>
      <br />
      <span class="subtask-content">{text}</span>
      <br />
      <span class="subtask-deadline">Due: {deadline}</span>
    </div>
    <div class="subtask-check">
      <input type="checkbox"></input>
    </div>
  </div>


  <!--<div class="subtask-wrap">
    <div class="subtask-text">{text}</div>
    <input class="subtask-radio" type="radio" name="{name}" value="pending">Pending</input>
    <input class="subtask-radio" type="radio" name="{name}" value="in-progress">In Progress</input>
    <input class="subtask-radio" type="radio" name="{name}" value="complete">Complete</input>
  </div>-->
</script>

</body>
</html>
