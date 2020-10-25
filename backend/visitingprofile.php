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
  $result->free();
  if ($checksecurity!=$_COOKIE['security']) {
    $response="Your Cookie is Fake";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }


  $visitID=$_POST['visitId'];
  $sql="SELECT `user_id`,`user_fname`,`user_lname`,`username`,`user_mail`,
  `user_gender`,`user_birth` FROM `6chan_users` WHERE `user_id`='{$visitID}';";
  $infoRetrive=$db->query($sql);
  if ($db->error) {
    $response="The info retrive query cannot be executed";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  if ($infoRetrive->num_rows==0) {
    $response="The user ID is fabricated";
    echo json_encode(['reply'=>true,'exist'=>false,'res'=>$response]);
    exit;
  }

  $info=$infoRetrive->fetch_assoc();

  $sql="SELECT 6chan_discussions.disc_id AS `disc_id`,6chan_discussions.disc_name AS `disc_name`,
  6chan_discussions.disc_category AS `disc_category`,6chan_discussions.disc_created_on AS `disc_created_on`,
  6chan_discussions.disc_members AS `disc_members` FROM `6chan_discussions` INNER JOIN `discussion_users`
   ON 6chan_discussions.disc_id=discussion_users.disc_id WHERE discussion_users.user_id='{$visitID}';";



   $discRetrive=$db->query($sql);
   if ($db->error) {
     $response="The forums retrive query cannot be executed";
     echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
     exit;
   }
   $joined=true;
   if ($discRetrive->num_rows==0) {
     $response="The user has not joined any discussions yet";
     $joined=false;
   }
   else {
     $forums_info=array();
     while($a=$discRetrive->fetch_assoc()){
       $forums_info[]=$a;
   }
 }

 $sql="SELECT * FROM `6chan_users_dp` WHERE `user_id`='{$visitID}';";
 $picResult=$db->query($sql);
 if ($db->error) {
   $response="The image retrive query cannot be executed";
   echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
   exit;
 }
 if ($picResult->num_rows==0) {
   if ($joined==true) {

     echo json_encode(['reply'=>true,'exist'=>true,'joined'=>$joined,'picture'=>false,'info'=>$info,'forum_list'=>$forums_info]);
     exit;
   }
   else {
     $response="The user has not joined any discussions yet";
     echo json_encode(['reply'=>true,'exist'=>true,'joined'=>$joined,'picture'=>false,'info'=>$info,'forum_list'=>$response]);
     exit;
   }
 }
 $picture=$picResult->fetch_assoc();
 $db->close();
 if ($joined==true) {
   echo json_encode(['reply'=>true,'exist'=>true,'joined'=>$joined,'picture'=>true,
   'picture_address'=>$picture['picture_info'],'info'=>$info,'forum_list'=>$forums_info]);
   exit;
 }
 else{
   $response="The user has not joined any discussions yet";
   echo json_encode(['reply'=>true,'exist'=>true,'joined'=>$joined,'picture'=>true,
   'picture_address'=>$picture['picture_info'],'info'=>$info,'forum_list'=>$response]);
   exit;
 }


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}
 ?>
