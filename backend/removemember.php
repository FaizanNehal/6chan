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
  $result->free();

  $discId=$_POST['discId'];
  $removeUser=$_POST['removeUser'];

  $sql="DELETE FROM `discussion_users` WHERE `user_id`='{$removeUser}' AND `disc_id`='{$discId}';";
  $removing=$db->query($sql);
  if ($db->error) {
    $response="Cannot Execute the remove user query";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }


  $sql="UPDATE `6chan_discussions` SET `disc_members`=`disc_members`-1 WHERE `disc_id`='{$discId}';";
  $decrement=$db->query($sql);
  if ($db->error) {
    $response=$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  $removing->free();
  $decrement->free();

  $response="User removes successfully";
   echo json_encode(['reply'=>true,'res'=>$response]);
   exit;


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
