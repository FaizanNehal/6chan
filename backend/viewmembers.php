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

  $sql="SELECT B.user_id AS `user_id`,B.user_fname AS `first_name`,B.user_lname AS
   `last_name` FROM `discussion_users` AS A INNER JOIN `6chan_users` AS B ON
   A.user_id=B.user_id WHERE A.disc_id='{$discId}';";

   $executeQuery=$db->query($sql);
   if ($db->error) {
     $response="Cannot process the members query";
     echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
     exit;
   }
   if ($executeQuery->num_rows==0) {
     $response="No user found";
     echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
     exit;
   }

   $members = array();
   while ($a=$executeQuery->fetch_assoc()) {
     $members[]=$a;
   }
   $sql="SELECT * FROM `6chan_discussions` WHERE `disc_id`='{$discId}' AND `disc_created_by`='{$userId}';";
   $adminquery=$db->query($sql);
   if ($db->error) {
     $response="Cannot execute the admin query";
     echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
     exit;
   }
   $admin=false;
   if ($adminquery->num_rows==0) {
     $admin=false;
   }
   else {
     $admin=true;
   }

   $adminquery->free();
   $db->close();

   echo json_encode(['reply'=>true,'userid'=>$userId,'admin'=>$admin,'members'=>$members]);
   exit;


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
