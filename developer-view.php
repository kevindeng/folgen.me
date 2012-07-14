<?php require_once('Connections/folgen.php'); ?>
<?php require('includes/getvaluestr.php'); ?>
<?php require_once('includes/restrict-access.php'); ?>

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
<?php include('includes/header.php') ?>


  <div id="main">
      <div id="task-controls">
        <span class="create-task button-controls">create a task</span>
        </div><!--end of task controls -->
   
      <div class="popup" id="popup-create-new-task">
          <h1>Creating a new task</h1>
          <div id="form-add-task-wrapper">
        <form id="form-add-task">
            <table width="340" border="0">
              <tr>
                <td align="left" valign="top">Task Title:</td>
                <td><input type="text" name="task-title" class="form-task"><br /></td>
              </tr>
              <tr>
                <td align="left" valign="top">Description:</td>
                <td><textarea name="task-description" class="form-task"></textarea><br /> </td>
              </tr>
              <tr>
                <td align="left" valign="top">Deadline:</td>
                <td><input name="task-deadline" type="text" class="form-task" id="task-datepicker"><br /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                <div  id="create-task-submit" button-controls> dont reload this shit for christ sake!</div></td>
              </tr>
            </table>
        </form>
        </div>  
       </div>
        <div id="form-add-task-result"></div>
       
        <div class="popup popup-create-new-subtask">
          <h1>Creating a new task</h1>
          <div id="form-add-subtask-wrapper">
        <form id="form-add-subtask">
            <table width="340" border="0">
              <tr>
                <td align="left" valign="top">Subtask Title:</td>
                <td><input type="text" name="subtask-title" class="form-task"><br /></td>
              </tr>
              <tr>
                <td align="left" valign="top">Description:</td>
                <td><textarea name="subtask-description" class="form-task"></textarea><br /> </td>
              </tr>
              <tr>
                <td align="left" valign="top">Deadline:</td>
                <td><input name="subtask-deadline" type="text" class="form-task" id="subtask-datepicker"><br /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                <div  class="create-subtask-submit button-controls"> create subtask!</div></td>
              </tr>
            </table>
        </form>
        </div>  
        <div id="form-add-subtask-result"></div>
        
        <br />
         
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
    $('#popup-create-new-task').fadeIn();
  });
  
 $('.create-subtask').click(function() {
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
    </table>
  </div>
  

    <!--<tr>
        <td colspan="3">
            <span class="task-title">{title}</span> 
            <br />
            <input type="text" class="form-task" value="task title">
            <span class="task-deadline">{deadline}</span>
            <span class="create-subtask button-controls" id="task-id">create subtask</span>
        </td>
    </tr>
    <tr>
      <td>
        <div class="task-subtasks"></div<
      </td>
    </tr>
    <tr>
      <td>
        <div class="task-comments"></div>
      </td>
    </tr>-->
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
