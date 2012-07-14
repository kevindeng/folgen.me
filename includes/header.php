<div id="header-wrapper">
  <div id="header">
      <div id="h-left">folgen.me</div>
        <div id="h-right"> Welcome, <?php  echo $_SESSION['MM_Username']; ?>
| <a href="<?php echo $logoutAction ?>">logout</a> Project #92839</div>
      <div class="clear"></div>
    </div><!-- end of header -->
</div><!-- header-wrapper -->

<div id="main-wrapper">
  <div id="main-menu">
        <ul>
          <li><a href="view-all.php">Manage all projects</a> | </li>
            <li><span class="add-new-project">Add new project</span></li>
        </ul>
    </div>

     <div class="popup" id="popup-create-new-project">
        	<h1>Creating a new task</h1>
          <div id="form-add-project-wrapper">
        <form id="form-add-project" action="ajax/add-project.php">
            <table width="340" border="0">
              <tr>
                <td align="left" valign="top">Project Title:</td>
                <td><input type="text" name="project-title" class="form-task"><br /></td>
              </tr>
              <tr>
                <td align="left" valign="top">Description:</td>
                <td><textarea rows="15" name="project-description" class="form-task"></textarea><br /> </td>
              </tr>
              <tr>
                <td align="left" valign="top">Start Date:</td>
                <td><input name="project-start" type="text" class="form-task" id="datepicker2"><br /></td>
              </tr>
              <tr>
                <td align="left" valign="top">Deadline:</td>
                <td><input name="project-deadline" type="text" class="form-task" id="datepicker"><br /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                <input type="button" id="create-project-submit" value="create task"></td>
              </tr>
            </table>
        </form>
        </div>  
        <div id="form-add-task-result"></div>
            
      </div>

      <script type="text/javascript">
		$(function() {
			$( "#datepicker" ).datepicker();
			$( "#datepicker2" ).datepicker();
		});
      function addProject(){
	      	$.ajax({
				type:'POST', 
				url: 'ajax/add-project.php', 
				data:$('#form-add-project').serialize(), 
				success: function(response) {
					console.log(response);
					console.log(response.indexOf('success'));
					if(response.indexOf('success') >= 0){
						$('#form-add-project-result').html("Project added with success!");
						$('#form-add-project-wrapper').slideUp('fast', function(){
							//animation complete
							var id = parseInt(response.substring(response.indexOf(':') + 1), 10);
							window.location.replace("../developer-view.php?project_id=" + id);
						});
					}else{
						$('#form-add-project-result').html("There was an error adding this task");
					}
		        	
		    	}
			});
		    return false;
		}
      $('#create-project-submit').click(function(){
      	 addProject();
      });
      $('.add-new-project').click(function(){
      	$('#popup-create-new-project').fadeIn();
      });
      </script>