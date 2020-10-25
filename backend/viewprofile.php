<?php
if (isset($_COOKIE['user_id'])&&isset($_COOKIE['security'])) {
  $userId=$_COOKIE['user_id'];
  $verify_user="SELECT * FROM `6chan_login` WHERE `user_id`='{$userId}';";
  $response;
  $db=new mysqli();
  @$db->connect('localhost','root','','6chan');
  if ($db->connect_error) {
    $response="Cannot connect to database";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  $result=$db->query($verify_user);
  $result_break;
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
    exit;
  }
  if ($result->num_rows==0) {
    $response="User is not found";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  $result_break=$result->fetch_assoc();




  $checksecurity=$result_break['user_id'].$result_break['user_password']."lord of the rings";
  $checksecurity=md5($checksecurity);
  $result->free();
  if ($checksecurity!=$_COOKIE['security']) {
    $response="Your Cookie is Fake";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }



  $retrive="SELECT * FROM `6chan_users` WHERE `user_id`='{$userId}';";
  $result=$db->query($retrive);
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
    exit;
  }
  if ($result->num_rows==0) {
    $reply="Cannot retrive user data";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
    exit;
  }

  $information=$result->fetch_assoc();
  $result->free();


  $retrive="SELECT 6chan_discussions.disc_id AS `disc_id`,6chan_discussions.disc_name AS `disc_name`,
  6chan_discussions.disc_category AS `disc_category`,6chan_discussions.disc_created_on AS `disc_created_on`,
  (SELECT COUNT(comment_id) FROM `disc_posts` WHERE disc_posts.disc_id=6chan_discussions.disc_id)
   AS `disc_comments` FROM `6chan_discussions` INNER JOIN `discussion_users`
   ON 6chan_discussions.disc_id=discussion_users.disc_id WHERE discussion_users.user_id='{$userId}';";

  $result=$db->query($retrive);
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
    exit;
  }
  $joined=true;
  if ($result->num_rows==0) {
    $reply="No, discussions have been joined yet";
    $joined=false;

  }
  else {
    $forums_info=array();
    while($a=$result->fetch_assoc()){
      $forums_info[]=$a;
  }

  }

  $sql="SELECT * FROM `6chan_users_dp` WHERE `user_id`='{$userId}';";
  $picResult=$db->query($sql);
  if ($db->error) {
    $reply="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
    exit;
  }
  if ($picResult->num_rows==0) {
    if ($joined==true) {
      echo json_encode(['reply'=>true,'joined'=>true,'picture'=>false,'info'=>$information,'forum_list'=>$forums_info]);
      exit;
    }
    else {
      echo json_encode(['reply'=>true,'joined'=>false,'picture'=>false,'info'=>$information,'forum_list'=>"No discussions have been joined"]);
      exit;
    }


  }
  $picture=$picResult->fetch_assoc();
  $db->close();
  if ($joined==true) {
    echo json_encode(['reply'=>true,'joined'=>true,'picture'=>true,'picture_address'=>$picture['picture_info'],
    'info'=>$information,'forum_list'=>$forums_info]);
    exit;
  }
  else {
    echo json_encode(['reply'=>true,'joined'=>false,'picture'=>true,'picture_address'=>$picture['picture_info'],
    'info'=>$information,'forum_list'=>"No discussions have been joined"]);
    exit;
  }


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}

 ?>
