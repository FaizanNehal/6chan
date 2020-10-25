<?php

if (isset($_COOKIE['user_id'])&&isset($_COOKIE['security'])){
  $userId=$_COOKIE['user_id'];
  $verify_user="SELECT * FROM `6chan_login` WHERE `user_id`='{$userId}';";
  $response;
  $db=new mysqli();
  @$db->connect('localhost','root','','6chan');
  if ($db->connect_error) {
    $response="Cannot connect to database";
    echo json_encode(['reply'=>false,'res'=>$response]);
    exit;
  }
  $result=$db->query($verify_user);
  $result_break;
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'res'=>$reply]);
    exit;
  }
  if ($result->num_rows==0) {
    $response="User is not found";
    echo json_encode(['reply'=>false,'res'=>$response]);
    exit;
  }
  $result_break=$result->fetch_assoc();




  $checksecurity=$result_break['user_id'].$result_break['user_password']."lord of the rings";
  $checksecurity=md5($checksecurity);
  $result->free();
  if ($checksecurity!=$_COOKIE['security']) {
    $response="Your Cookie is Fake";
    echo json_encode(['reply'=>false,'res'=>$response]);
    exit;
  }

  if (isset($_POST['manage'])) {
    $sql="SELECT 6chan_discussions.disc_id AS `disc_id`,6chan_discussions.disc_name AS `disc_name`,
    6chan_discussions.disc_category AS `disc_category`,6chan_discussions.disc_created_on AS
    `disc_created_on`,(SELECT COUNT(comment_id) FROM `disc_posts` WHERE disc_posts.disc_id=6chan_discussions.disc_id)
     AS `disc_comments` FROM `6chan_discussions` WHERE `disc_created_by`='{$userId}';";
    $manage=$db->query($sql);
    if ($db->error) {
      $response="Manage Discussion query cannot be executed";
      echo json_encode(['reply'=>false,'res'=>$response]);
      exit;
    }
    if ($manage->num_rows==0) {
      $response="Manage Discussion query cannot be executed";
      $manage->free();
      $db->close();
      echo json_encode(['reply'=>true,'query'=>false,'res'=>$response]);
      exit;
    }
    $managelist = array();
    while ($a=$manage->fetch_assoc()) {
      $managelist[]=$a;
    }
    $manage->free();
    $db->close();
    echo json_encode(['reply'=>true,'query'=>true,'forums'=>$managelist]);
    exit;

  }

  $retrive="SELECT 6chan_discussions.disc_id AS `disc_id`,6chan_discussions.disc_name AS `disc_name`,
  6chan_discussions.disc_category AS `disc_category`,6chan_discussions.disc_created_on AS `disc_created_on`,
  (SELECT COUNT(comment_id) FROM `disc_posts` WHERE disc_posts.disc_id=6chan_discussions.disc_id)
   AS `disc_comments` FROM `6chan_discussions` INNER JOIN `discussion_users`
   ON 6chan_discussions.disc_id=discussion_users.disc_id WHERE discussion_users.user_id='{$userId}';";

  $result=$db->query($retrive);
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'res'=>$reply]);
    exit;
  }
  if ($result->num_rows==0) {
    $response="This user have not joined any discussion yet";
    $result->free();
    $db->close();
    echo json_encode(['reply'=>true,'query'=>false,'res'=>$response]);
    exit;
  }

  $forums = array();
  while ($a=$result->fetch_assoc()) {
    $forums[]=$a;
  }
  $result->free();
  $db->close();
  echo json_encode(['reply'=>true,'query'=>true,'forums'=>$forums]);
  exit;
}
else {

}
 ?>
