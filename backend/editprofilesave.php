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

if ($_FILES['image']) {
  $imgName=$_FILES['image']['name'];
  $imgNameBreak=explode('.',$imgName);
  $imgName=array_pop($imgNameBreak);

  $imgName=$userId.'-dp'.'.'.$imgName;
  $_FILES['image']['name']=$imgName;
  $path="./../assets/profiles/".$userId;
  if (!file_exists($path)) {
    mkdir($path);
    move_uploaded_file($_FILES['image']['tmp_name'],$path.'/'.$_FILES['image']['name']);
    $sql="INSERT INTO `6chan_users_dp` SET `user_id`='{$userId}',`picture_info`='{$imgName}';";
  }
  else {
    move_uploaded_file($_FILES['image']['tmp_name'],$path.'/'.$_FILES['image']['name']);
    $sql="UPDATE `6chan_users_dp` SET `picture_info`='{$imgName}' WHERE `user_id`='{$userId}';";
  }


  $db->query($sql);
  if ($db->error) {
    $response="$db->error";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  echo json_encode(['reply'=>true,'res'=>'Image Uploaded Succesfully']);
  exit;
}


if ($_POST['changePassword']) {
  $oldPassword=$_POST['oldPassword'];
  $newPassword=$_POST['newPassword'];
  $oldPassword=addslashes($oldPassword);
  $newPassword=addslashes($newPassword);
  $oldPassword=md5($oldPassword);
  $newPassword=md5($newPassword);


  $sql="SELECT * FROM `6chan_login` WHERE `user_id`='{$userId}';";
  $passwordCheck=$db->query($sql);
  if ($db->error) {
    $reponse="Cannot execute the password check query";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  $passwordInfo=$passwordCheck->fetch_assoc();
  if ($oldPassword!=$passwordInfo['user_password']) {
    $response="The password you entered is incorrect";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  $sql="UPDATE `6chan_login` SET `user_password`={$newPassword} WHERE `user_id`='{$userId}';";
  $updatingPassword=$db->query($sql);
  if ($db->error) {
    $response="login password update query failed";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  $sql="UPDATE `6chan_users` SET `user_password`={$newPassword} WHERE `user_id`='{$userId}';";
  $updatingPassword2=$db->query($sql);
  if ($db->error) {
    $response="users password update query failed";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }


  $updatingPassword->free();
  $updatingPassword2->free();
  $db->close();

  $response="Password successfully changed";
  echo json_decode(['reply'=>true,'res'=>$response]);
  exit;
}

$userName=$_POST['userName'];
$firstName=$_POST['firstName'];
$lastName=$_POST['lastName'];
$email=$_POST['email'];
$userName=addslashes($userName);
$firstName=addslashes($firstName);
$lastName=addslashes($lastName);
$email=addslashes($email);




$usernameChange=false;
$usernameResponse=false;
if ($_POST['changeUsername']) {
  $sql="SELECT * FROM `6chan_users` WHERE `username`='{$userName}';";
  $checking=$db->query($sql);
  if ($db->error) {
    $response="The username and email checking query cannot be executed";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  if ($checking->num_rows>0) {
    $usernameChange=false;
    $usernameResponse="This username is already taken please try another one";
  }
  else {

    $sql="UPDATE `6chan_users` SET `username`='{$userName}' WHERE `user_id`='{$userId}';";
    $updatingUsername=$db->query($sql);
    if ($db->error) {
      $response="The user mail update query cannot be executed";
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    $usernameChange=true;
    $usernameResponse="Username has been successfully changed";
  }
}


$usermailChange=false;
$usermailResponse=false;
if ($_POST['changeEmail']) {
  $sql="SELECT * FROM `6chan_users` WHERE  `user_mail`='{$email}';";
  $checkingmail=$db->query($sql);
  if ($db->error) {
    $response="The username and email checking query cannot be executed";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  if ($checkingmail->num_rows>0) {
    $usermailChange=false;
    $usermailResponse="This email address already have an account";
  }
  else {
    $sql="UPDATE `6chan_login` SET `user_mail`='{$email}' WHERE `user_id`='{$userId}';";
    $checking2=$db->query($sql);
    if ($db->error) {
      $response="The email update query cannot be executed";
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    $sql="UPDATE `6chan_users` SET `user_mail`='{$email}' WHERE `user_id`='{$userId}';";
    $updatingUsermail=$db->query($sql);
    if ($db->error) {
      $response="The user mail update query cannot be executed";
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    $usermailChange=true;
    $usermailResponse="Your email address has been successfully changed";
  }
}




$sql="UPDATE `6chan_users` SET `user_fname`='{$firstName}',`user_lname`='{$lastName}' WHERE `user_id`='{$userId}';";
$updatingLogin=$db->query($sql);
if ($db->error) {
  $response="The user info update query cannot be executed";
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}


$db->close();

$response="Everything executed and updated successfully";
echo json_encode(['reply'=>true,'usernameChange'=>$usernameChange,'usernameResponse'=>$usernameResponse,
'usermailChange'=>$usermailChange,'usermailResponse'=>$usermailResponse,'res'=>$response]);
exit;


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
