<?php

if (isset($_COOKIE['user_id'])&&isset($_COOKIE['security'])){
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

  if ($checksecurity!=$_COOKIE['security']) {
    $response="Your Cookie is Fake";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }


  $reply=$_POST['reply'];
  $postId=$_POST['postId'];
  $discId=$_POST['discId'];

  $sql="INSERT INTO `6chan_posts_reply` SET `reply`='{$reply}',`post_id`='{$postId}',`disc_id`='{$discId}',`user_id`='{$userId}';";
  $execute=$db->query($sql);
  if ($db->error) {
    $response=$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  $db->close();
  $response='Reply added to the database successfully';

  echo json_encode(['reply'=>true,'res'=>$response]);
  exit;
}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}
 ?>
