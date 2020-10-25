<?php
$db=new mysqli();
@$db->connect("localhost","root","","6chan");
$response;
if($db->connect_error){
  $response="Cannot Connect to the database";
  echo json_encode(['res'=>false,'reply'=>$response]);
  exit;
}
$fname=$_POST['fname'];
$lname=$_POST['lname'];
$uname=$_POST['uname'];
$mail=$_POST['mail'];
$password=$_POST['password'];
$dob=$_POST['dob'];
$gender=$_POST['gender'];
$imageUpload=$_POST['imageUpload'];
$uname=addslashes($uname);
$mail=addslashes($mail);

$sqlmail="SELECT `user_mail` FROM `6chan_users` WHERE `user_mail`='{$mail}';";
$sqlname="SELECT `username` FROM `6chan_users` WHERE `user_mail`='{$uname}';";
$mailRExp="^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$";
$pasRExp="(?=.{8,})";

$result=$db->query($sqlmail);
if($result->num_rows!=0){
  $response="User Already Exits. Please enter another E-mail";
  echo json_encode(['res'=>false,'reply'=>$response]);
  exit;
}


$result1=$db->query($sqlname);
if($result1->num_rows!=0){
  $response="UserName Already Taken. Please enter another User Name";
  echo json_encode(['res'=>false,'reply'=>$response]);
  exit;
}


$fname=addslashes($fname);
$lname=addslashes($lname);
$password=addslashes($password);
$gender=addslashes($gender);
$dob=addslashes($dob);


$password=md5($password);

$sql="INSERT INTO `6chan_login` SET `user_mail`='{$mail}',`user_password`='{$password}';";
$result2=$db->query($sql);
if($db->error){
  $response="Cannot Insert Into the Login Table";
  echo json_encode(['res'=>false,'reply'=>$response]);
  exit;
}

$sql="INSERT INTO `6chan_users` SET `user_fname`='{$fname}',`user_lname`='{$lname}',`username`='{$uname}',
`user_mail`='{$mail}',`user_gender`='{$gender}',`user_password`='{$password}',`user_birth`='{$dob}';";

$res=$db->query($sql);
if($db->error){
  $response="Cannot insert the data into the Users Table";
  echo json_encode(['res'=>false,'reply'=>$response]);
  exit;
}

$lastID=$db->insert_id;

if ($_FILES['image']) {
  $imgName=$_FILES['image']['name'];
  $imgNameBreak=explode('.',$imgName);
  $imgName=array_pop($imgNameBreak);




  $imgName=$lastID.'-dp'.'.'.$imgName;
  $path="./../assets/profiles/".$lastID;
  if (!file_exists($path)) {
    mkdir($path);
  }

  $_FILES['image']['name']=$imgName;
  move_uploaded_file($_FILES['image']['tmp_name'],$path.'/'.$_FILES['image']['name']);
  $sql="INSERT INTO `6chan_users_dp` SET `user_id`='{$lastID}',`picture_info`='{$imgName}';";
  $db->query($sql);
  if ($db->error) {
    $response="Cannot insert the profile picture";
    echo json_encode(['res'=>false,'reply'=>$response]);
    exit;
  }
}


setcookie('user_id',$lastID,0);
$security=$lastID.$password."lord of the rings";
$security=md5($security);
setcookie('security',$security,time()+7200);

$result->free();
$result1->free();
$result2->free();
$res->free();
$db->close();
echo json_encode(['res'=>true,'reply'=>'inserted']);
exit;
 ?>
