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

  if ($checksecurity!=$_COOKIE['security']) {
    $response="Your Cookie is Fake";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

$discussionId=$_POST['joinID'];
$date=date("Y-m-d");



$sqlcheck="SELECT * FROM `discussion_users` WHERE `user_id`='{$userId}' AND `disc_id`='{$discussionId}';";


  $result2=$db->query($sqlcheck);
  if($db->error){
    $response="Cannot process the sql query ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  if($result2->num_rows!=0){
    $response="Already joined";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

$sql="SELECT *  FROM `6chan_discussions` WHERE `disc_id`='{$discussionId}';";
$discresult=$db->query($sql);
if($db->error){
  $response="Cannot process the sql query ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$fetching=$discresult->fetch_assoc();
$discussionName=$fetching['disc_name'];


$sqljoin="INSERT INTO `discussion_users` SET `user_id`='{$userId}',`disc_id`='{$discussionId}',
`disc_name`='{$discussionName}',`joined_on`='{$date}';";



$final=$db->query($sqljoin);
if($db->error){
  $response="Cannot process the sql query ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$sql="UPDATE `6chan_discussions` SET `disc_members`=`disc_members`+1 WHERE `disc_id`='{$discussionId}';";
$increment=$db->query($sql);
if ($db->error) {
  $response=$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$final->free();
$increment->free();

$db->close();
$response="Joined";
echo json_encode(['reply'=>true,'res'=>$response]);
exit;

}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}






 ?>
