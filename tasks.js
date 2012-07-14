var Y = YUI({useSync: true}).use('substitute');

if(!folgen)
  var folgen = {};

//-------

if(!folgen.utils)
  folgen.utils = {};

folgen.utils.dateProgress = function(d, start, end) {
  return (d.getTime() - start.getTime()) / 
    (end.getTime() - start.getTime());
}

//-------

folgen.Subtask = function(data) {
  for(var x in data)
    this[x] = data[x];
}

folgen.Subtask.templateName = 'subtask-template';

folgen.Subtask.prototype.renderAsElement = function() {
  var e = $(Y.substitute($('#' + folgen.Subtask.templateName).html(), {
    text: this.title,
    deadline: this.deadline
  }));
  return e;
}

//-------

folgen.Task = function(data) {
  for(var x in data)
    this[x] = data[x];
  if(!folgen.Task.templateText)
    folgen.Task.templateText = $('#' + folgen.Task.templateName).html();
  var dateStr = this.deadline.substring(0, this.deadline.indexOf(' '));
  this.deadline = $.datepicker.parseDate('yy-mm-dd', dateStr);
}

folgen.Task.templateName = 'task-template';
folgen.Task.commentsContainer = 'task-comments';
folgen.Task.subtaskContainer = 'task-subtasks';

folgen.Task.prototype.renderAsElement = function() {
  // render main
  var e = $(Y.substitute(folgen.Task.templateText, {
    title: this.title,
    description: this.description,
    deadline: $.datepicker.formatDate('M d, yy', this.deadline)
  }));

   // render subtasks
  this.subtaskContainer = e.find('.' + folgen.Task.subtaskContainer);
  for(var i = 0; i < this.subtasks.length; i++) {
    var subtask = new folgen.Subtask(this.subtasks[i]);
    var subtaskEle = subtask.renderAsElement();
    this.subtaskContainer.append(subtaskEle);
  }

  var sc = this.subtaskContainer;
	e.find('.create-subtask').click($.proxy(function() {
    	$('.popup-create-new-subtask').fadeIn();
      $('.create-subtask-submit').click($.proxy(function(){
        /*$.ajax('data.php', {
          type:'POST', 
          data: {
            op: 'add_subtask',
            task_id: this.id,
            text: $('#form-add-subtask input').val(),
            deadline: $('#form-add-subtask input:last').val()
          }, 
          success: $.proxy(function(response) {
            if(response){
              $('#form-add-subtask-result').html("Subtask added with success!");
              $('#form-add-subtask-wrapper').slideUp('fast', $.proxy(function(){
                //animation complete
                var a = new folgen.Subtask(
                JSON.parse(response)).renderAsElement();
                $(a).hide();
                sc.prepend(a);
                a.fadeIn();
                $('.popup-create-new-subtask').fadeOut();
              }, this));
            }
            else {
              $('#form-add-subtask-result').html("There was an error adding this task");
            }
          }, this)
        });*/
        var data = {
            op: 'add_subtask',
            task_id: this.id,
            title: $('#form-add-subtask input').val(),
            deadline: $('#form-add-subtask input:last').val()
        };
        var b = new folgen.Subtask(data);
        var a = b.renderAsElement();
        a.find('input').change(function(){
            if(window.__prog)
              window.__prog++;
            else
              window.__prog = 1;
            window.__proj.moveProgressBar();
          }); 
        a.hide();
          sc.prepend(a);
          a.fadeIn();
          $('.popup-create-new-subtask').fadeOut();
      }, this));
  }, this));

  // render comments
  this.commentsContainer = e.find('.' + folgen.Task.commentsContainer);
  for(var i = 0; i < this.comments.length; i++) {
    var comment = new folgen.Comment(this.comments[i]);
    var commentEle = comment.renderAsElement();
    this.commentsContainer.append(commentEle);
  }

  // bind controls to comment textbox/button
  e.find('.task-comment-add button').click($.proxy(function() {
    var txt = e.find('.task-comment-add textarea').val();
    $.ajax('data.php', {
      method: 'POST',
      data: {
        op: 'add_comment_task',
        user_id: parseInt($('#__uid').val(), 10),
        task_id: this.id,
        text: txt
      },
      success: $.proxy(function(response) {
        var comment = new folgen.Comment(JSON.parse(response));
        var nc = $(comment.renderAsElement());
        nc.hide();
        this.commentsContainer.prepend(nc);
        nc.fadeIn(1000);
      }, this)
    });
  }, this));

  return e;
}

//-------

folgen.Comment = function(data) {
  for(var x in data)
    this[x] = data[x];
}

folgen.Comment.templateName = 'comment-template';

folgen.Comment.prototype.renderAsElement = function() {
  var e = $(Y.substitute($('#' + folgen.Comment.templateName).html(), {
    imgSrc: this.user.img,
    user: this.user.fullname,
    text: this.text,
    time: this.timestamp
  }));
  return e;
}

//---------

folgen.Project = function(data) {
  for(var x in data)
    this[x] = data[x];
  this.start = $.datepicker.parseDate('yy-mm-dd', this.start);
  this.deadline = $.datepicker.parseDate('yy-mm-dd', this.deadline)
  var tmp = [];
  for(var i = 0; i < this.tasks.length; i++)
    tmp.push(new folgen.Task(this.tasks[i]));
  this.tasks = tmp;
  window.__proj = this;
}

folgen.Project.templateName = 'project-template';
folgen.Project.taskContainer = 'project-tasks';

folgen.Project.loadInto = function(projectId, renderTarget) {
  $.ajax('data.php', {
    data: {
      op: 'get_project',
      project_id: projectId
    },
    success: function(data) {
      var p = new folgen.Project(JSON.parse(data));
      renderTarget.append(p.renderAsElement());
      p.moveProgressBar();
    }
  });
}

folgen.Project.prototype.getProgress = function() {
  var numTasks = this.tasks.length;
  var progress = 0;
  for(var i = 0; i < this.tasks.length; i++) {
    var subTasksComplete = 0;
    var numSubtasks = this.tasks[i].subtasks.length;
    for(var j = 0; j < numSubtasks; j++)
      if(this.tasks[i].subtasks[j].complete)
        subTasksComplete++;
    var sub = subTasksComplete / numSubtasks;
    if(!isNaN(sub))
      progress += (1 / numTasks) * sub;
    else if(parseInt(this.tasks[i].complete, 10) > 0)
      progress += 1 / numTasks;
  }
  return progress;
}

folgen.Project.prototype.moveProgressBar = function() {
  if(!this.prevVal)
    this.prevVal = 0;
  var progress = window.__prog / 3;

  //var progress = this.getProgress();
  var totalWidth = this.el.find('#progress-bar').width();
  var fillWidth = progress * totalWidth;
  $('#progress-bar-fill').animate({
    width: fillWidth.toString()
  }, 3000);
}

folgen.Project.prototype.renderAsElement = function() {
  var e = $(Y.substitute($('#' + folgen.Project.templateName).html(), {
    title: this.title,
    description: this.description,
    deadline: $.datepicker.formatDate('M d, yy', this.deadline),
    start: $.datepicker.formatDate('M d, yy', this.start),
    remaining: Math.ceil((this.deadline.getTime() - 
      new Date().getTime()) / (24 * 3600000)).toString() + ' days',
    progress: Math.floor(this.getProgress() * 100).toString()
  }));

  // render tasks
  for(var i = 0; i < this.tasks.length; i++) {
    var taskContainer = e.find('.' + folgen.Project.taskContainer);
    taskContainer.append(this.tasks[i].renderAsElement());
  }

  // move the "today" arrow and money count
  var dp = folgen.utils.dateProgress(new Date(), this.start, this.deadline);
  e.find('#arrow-tracker').css('margin-left', 960 * dp);
  e.find('#progress-bar-amount').css('margin-left', 960 * dp - 40);

  // mark deadlines
  for(var i = 0; i < this.tasks.length; i++) {
    var label = $('<div class="progress-date-label">' + 
      $.datepicker.formatDate('M d, yy', this.tasks[i].deadline) + '</div>');
    label.css('margin-left', 960 * folgen.utils.dateProgress(
      this.tasks[i].deadline, this.start, this.deadline));
    e.find('#progress-bar-today-tracker').prepend(label);
  }

  this.el = e;
  return this.el;
}


