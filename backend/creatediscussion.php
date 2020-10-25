<?php

$discussionName=$_POST['discName'];
$discussionCat=$_POST['discCat'];
$discussionDesp=$_POST['discDesp'];
$date=date("Y-m-d");
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




$reply;

$discussionName=addslashes($discussionName);
$discussionCat=addslashes($discussionCat);
$discussionDesp=addslashes($discussionDesp);
$date=addslashes($date);

$sql="INSERT INTO `6chan_discussions` SET `disc_name`='{$discussionName}',`disc_category`='{$discussionCat}',
`disc_created_by`='{$userId}',`disc_created_on`='{$date}',`disc_members`=1,`disc_description`='{$discussionDesp}';";


$checksql="SELECT * FROM `6chan_discussions` WHERE `disc_name`='{$discussionName}';";
$result2=$db->query($checksql);
if($result2->num_rows!=0){
  $array=$result2->fetch_array();
  $id=$array['disc_id'];
  $response="This Forum already exists";
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$db->query($sql);
if($db->error){
  $response="Cannot execute the query  ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$result3=$db->query("SELECT `disc_id` FROM `6chan_discussions` WHERE `disc_name`='{$discussionName}'; ");
$id=$result3->fetch_assoc();
$disc_id=$id['disc_id'];

$sql="INSERT INTO `discussion_users` SET `user_id`='{$userId}',`disc_id`='{$disc_id}',
`disc_name`='{$discussionName}',`joined_on`='{$date}';";
$db->query($sql);
if($db->error){
  $response="Cannot execute the query  ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}
$result3->free();
$db->close();
$reply="'".$discussionName. "' has been Successfully Created";
echo json_encode(['reply'=>true,'res'=>$reply]);
exit;
}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}



 ?>
