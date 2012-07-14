<?php

if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "long":
      case "int":
        $theValue = ($theValue != "") ? intval($theValue) : "NULL";
        break;
      case "double":
        $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
        break;
      case "date":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "defined":
        $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

mysql_pconnect("folgenme.db.7851672.hostedresource.com", 
  "folgenme", "YahooUR2012") or die(mysql_error());
mysql_select_db("folgenme") or die(mysql_error());

/*------------------- utilities ----------------------*/

function getFirst($q) {
  $r = mysql_query($q);
  return mysql_fetch_assoc($r);
}

function getAll($q) {
  $r = mysql_query($q);
  $a = [];
  do {
    $f = mysql_fetch_assoc($r);
    if ($f) {
      $a[] = $f;
    }
    else
      break;
  } while (true);
  return $a;
}

function getArray($q) {
  $r = mysql_query($q);
  $a = [];
  do {
    $f = mysql_fetch_array($r);
    if ($f) 
      $a[] = $f[0];
    else
      break;
  } while (true);
  return $a;
}

/*--------------------- inserts ----------------------*/

function createUser($name, $email, $password, $img) {
  $q = sprintf("INSERT INTO user (id, fullname, email, password)" . 
        " VALUES (NULL, %s, %s, %s, %s)",
        GetSQLValueString($name, "text"),
        GetSQLValueString($email, "text"),
        GetSQLValueString($password, "text"),
        GetSQLValueString($img, "text"));
  mysql_query($q);
  return mysql_insert_id();
}

function createTask($title, $summary, $description, $start,
  $deadline, $priority, $cost) {
  $q = sprintf(
    "INSERT INTO task VALUES (NULL, %s, %s, %s, %s, %s, 0, %d, %d)",
    GetSQLValueString($title, "text"), 
    GetSQLValueString($summary, "text"), 
    GetSQLValueString($description, "text"), 
    GetSQLValueString($start, "text"), 
    GetSQLValueString($deadline, "text"), 
    GetSQLValueString($priority, "int"), 
    GetSQLValueString($cost, "int") 
    );
  mysql_query($q);
  return mysql_insert_id();
}

function createComment($userId, $text) {
  $q = sprintf(
    "INSERT INTO comment VALUES (NULL, %d, %s, NULL)",
    GetSQLValueString($userId, "int"),
    GetSQLValueString($text, "text"));
  mysql_query($q);
  return mysql_insert_id();
}

/*---------------- lowest level gets -----------------*/

function getUser($userId) {
  return getFirst(sprintf("SELECT * FROM user WHERE id=%d", $userId));
}

function getSubtask($subtaskId) {
  return getFirst(sprintf("SELECT * FROM subtask WHERE id=%d", $subtaskId));
}

/*---------------- association gets -----------------*/

function getCommentIdsForTask($taskId) {
  return getArray(sprintf("SELECT comment_id FROM comment_to_task WHERE task_id=%d ORDER BY comment_id DESC", $taskId));
}

function getSubtaskIdsForTask($taskId) {
  return getArray(sprintf("SELECT subtask_id FROM subtask_to_task WHERE task_id=%d ORDER BY subtask_id DESC", $taskId));
}

function getTaskIdsForProject($projectId) {
  return getArray(sprintf("SELECT task_id FROM task_to_project WHERE project_id=%d ORDER BY task_id DESC", $projectId));
}

/*------------------- compound gets -----------------*/

function getComment($commentId) {
  $comment = getFirst(sprintf("SELECT * FROM comment WHERE id=%d", $commentId));
  $user = getUser($comment['user_id']);
  $comment['user'] = $user;
  return $comment;
}

function getTask($taskId) {
  $task = getFirst(sprintf("SELECT * FROM task WHERE id=%d", $taskId));
  $subtasks = array_map("getSubtask", getSubtaskIdsForTask($taskId));
  $comments = array_map("getComment", getCommentIdsForTask($taskId));
  $task['subtasks'] = $subtasks;
  $task['comments'] = $comments;
  return $task;
}

function getProject($projectId) {
  $project = getFirst(sprintf("SELECT * FROM project WHERE id=%d", $projectId));
  $tasks = array_map("getTask", getTaskIdsForProject($projectId));
  $project['tasks'] = $tasks;
  return $project;
}

/* ---------------- complex inserts ------------------ */
function commentOnTask($userId, $text, $taskId) {
  $commentId = createComment($userId, $text);
  $q = sprintf(
    "INSERT INTO comment_to_task VALUES (NULL, %d, %d)",
    GetSQLValueString($commentId),
    GetSQLValueString($taskId));
  mysql_query($q);
  return getComment($commentId);
}

function commentOnProject($userId, $text, $projectId) {
  $commentId = createComment($userId, $text);
  $q = sprintf(
    "INSERT INTO comment_to_project VALUES (NULL, %d, %d)",
    GetSQLValueString($commentId),
    GetSQLValueString($projectId));
  mysql_query($q);
  return getComment($commentId);
}

function createSubtask($taskId, $title, $deadline) {
  $q = sprintf("INSERT INTO subtask VALUES (%d, NULL, %s, 0, %s)",
    GetSQLValueString($taskId, "int"),
    GetSQLValueString($title, "text"),
    GetSQLValueString($deadline, "text"));
  echo $q;
  mysql_query($q);
  $stId = mysql_insert_id();
  $q = sprintf("INSERT INTO subtask_to_task VALUES (NULL, %d, %d, NULL)",
    $taskId, $stId);
  mysql_query($q);
  return getSubtask($stId);
}

/*------------ ----------- --------------*/



function getUserTasks($userId) {
  $q = sprintf(
    "SELECT * FROM task INNER JOIN user_to_task ON " . 
      "task.id=user_to_task.task_id WHERE user_to_task.user_id=%d",
    GetSQLValueString($userId, "int"));
  return getJson($q);
}

function assignUserToTask($userId, $taskId) {
  $q = sprintf(
    "INSERT INTO user_to_task VALUES (NULL, %d, %d, NULL)",
    GetSQLValueString($userId, "int"),
    GetSQLValueString($taskId, "int")
    );
  echo $q;
  mysql_query($q);
}


/*function getComment($commentId) {
  $basic = getFirst(sprintf("SELECT * FROM commet WHERE id=%d", $commentId));
  $user = getUser($basic['user_id']);
  $basic['user'] = $user;
  return $basic;
}*/

function getTaskSubtasks($taskId) {
  $q = sprintf(
    "SELECT * FROM subtask INNER JOIN subtask_to_task ON " . 
      "subtask.id=subtask_to_task.subtask_id " . 
      "WHERE subtask_to_task.task_id=%d",
    GetSQLValueString($taskId, "int"));
  $r = mysql_query($q);
  $a = [];
  do {
    $f = mysql_fetch_assoc($r);
    if ($f)
      $a[] = $f;
    else
      break;
  } while (true);
  return $a;
}



function getTaskComments($taskId) {
  $q = sprintf(
    "SELECT * FROM comment INNER JOIN comment_to_task ON " . 
      "comment.id=comment_to_task.comment_id " . 
      "WHERE comment_to_task.task_id=%d",
    GetSQLValueString($taskId, "int"));
  $r = mysql_query($q);
  $a = [];
  do {
    $f = mysql_fetch_assoc($r);
    if ($f)
      $a[] = $f;
    else
      break;
  } while (true);
  return $a;
}

//-----------

function main($args) {
  $op = $args['op'];
  if ($op == 'add_task') {
    createTask(
      $args['title'],
      $args['summary'],
      $args['description'],
      $args['start'],
      $args['deadline'],
      $args['priority'],
      $args['cost']);
  }
  else if ($op == 'ass_user_task') {
    assignUserToTask($args['user_id'], $args['task_id']);
  }
  else if ($op == 'get_user_tasks') {
    echo getUserTasks($args['user_id']);
  }
  else if ($op == 'get_task_comments') {
    $a = getTaskComments($args['task_id']);
    echo json_encode($a);
  }
  else if ($op == 'get_task_full') {
    echo json_encode(getTask($args['task_id']));
  }
  else if ($op == 'get_project') {
    echo json_encode(getProject($args['project_id']));
  }
  else if ($op == 'add_comment_task') {
    echo json_encode(commentOnTask($args['user_id'],
      $args['text'], $args['task_id']));
  }
  else if ($op == 'add_subtask') {
    echo json_encode(createSubtask($args['task_id'],
      $args['text'], $args['deadline']));
  }
  else {
    echo "Invalid op";
  }
}

main($_GET ? $_GET : $_POST);

?>