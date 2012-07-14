<div id="header-wrapper">
  <div id="header">
      <div id="h-left">folgen.me</div>
        <div id="h-right"> Welcome, <?php  echo $_SESSION['MM_Username']; ?>
| <a href="<?php echo $logoutAction ?>">logout</a> Project #92839</div>
      <div class="clear"></div>
    </div><!-- end of header -->
</div><!-- header-wrapper -->

<div id="main-wrapper">

  
            
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
					//console.log(response);
					///console.log(response.indexOf('success'));
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
		  $('.popup').hide();
      	$('#popup-create-new-project').fadeIn();
      });
      </script>