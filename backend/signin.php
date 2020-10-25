<?php
$db=new mysqli();
@$db->connect("localhost","root","","6chan");
$response;
if($db->connect_error){
  $response=$db->connect_error;
  echo json_encode(['code'=>false,'response'=>$response]);
  exit;
}
$mail=$_POST['mail'];
$password=$_POST['password'];
$mode=$_POST['mode'];

$mail=addslashes($mail);
$password=addslashes($password);
$password=md5($password);
$user_id;
$sql;
$response;
$array;

if ($mode==1) {
$sql="SELECT * FROM `6chan_admins` WHERE `user_mail`='{$mail}';";
$result=$db->query($sql);
if ($db->error) {
  $response="$db->error";
  echo json_encode(['code'=>false,'response'=>$response]);
  exit;
}
if ($result->num_rows==0) {
  $response="This user does not exists, Please Create an account first";
  echo json_encode(['code'=>false,'response'=>$response]);
  exit;
}
$db->close();
$array=$result->fetch_array();

if ($array['user_password']!=$password) {
  $response="The password that you entered is incorrect";
  echo json_encode(['code'=>false,'response'=>$response]);
  exit;
}
else {
  $response="Success";
  $_SESSION['id']=$array['user_id'];
  $_SESSION['password']="Lord of the Rings";
  echo json_encode(['code'=>true,'response'=>$response]);
  exit;
}

}
else {
  $sql="SELECT * FROM `6chan_login` WHERE `user_mail`='{$mail}';";
  $result=$db->query($sql);
  if ($db->error) {
    $response="$db->error";
    echo json_encode(['code'=>false,'response'=>$response]);
    exit;
  }
  if ($result->num_rows==0) {
    $response="This user does not exists, Please Create an account first";
    echo json_encode(['code'=>false,'response'=>$response]);
    exit;
  }
  $db->close();
  $array=$result->fetch_assoc();

  if ($array['user_password']!=$password) {
    $response="The password that you entered is incorrect";
    echo json_encode(['code'=>false,'response'=>$response]);
    exit;
  }
  else {
    $response="Success";
    $user_id=$array['user_id'];
    setcookie('user_id',$user_id,0);
    $security=$user_id.$password."lord of the rings";
    $security=md5($security);
    setcookie('security',$security,time()+7200);
    echo json_encode(['code'=>true,'response'=>$response]);
    exit;
  }

}

 ?>
