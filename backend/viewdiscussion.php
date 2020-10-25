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

$pageid=$_POST['pageid'];
$pageno=$_POST['pageno'];
$n=20;


$sql="SELECT * FROM `6chan_discussions` WHERE `disc_id`='{$pageid}';";
$executeQuery=$db->query($sql);
if ($db->error) {
  $reply="Cannot process the sql query ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
  exit;
}
if ($executeQuery->num_rows==0) {
  $response="This discussion is not found, please try again";
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}

$information=$executeQuery->fetch_assoc();
$executeQuery->free();


$sql="SELECT A.comment_id AS `comment_id`, B.user_id AS `user_id`,B.username AS
 `username`,A.comment AS `comment`,A.date_posted AS `date`,C.picture_info AS
 `picture_info`,D.picture_info AS `post_picture_info` FROM `disc_posts` AS A INNER JOIN
 `6chan_users` AS B ON A.comment_by=B.user_id LEFT OUTER JOIN `6chan_users_dp` AS C
  ON B.user_id=C.user_id LEFT OUTER JOIN `6chan_posts_picture` AS D ON A.comment_id=D.post_id WHERE A.disc_id='{$pageid}'
   ORDER BY A.comment_id" ;


$executePostQuery=$db->query($sql);
if ($db->error) {
  $reply="Cannot process the sql query ".$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
  exit;
}
if ($executePostQuery->num_rows==0) {
  $response="No post or comment has been added up till now";
  echo json_encode(['reply'=>true,'postReply'=>false,'disc_info'=>$information]);
  exit;
}
$rows=$executePostQuery->num_rows;
$pages=ceil($rows/$n);
if ($pageno==0) {
  $m=($pages-1)*$n;
}
else {
  $m=($pageno-1)*$n;
}
$sql="SELECT A.comment_id AS `comment_id`, B.user_id AS `user_id`,B.username AS
 `username`,A.comment AS `comment`,A.date_posted AS `date`,C.picture_info AS
 `picture_info`,D.picture_info AS `post_picture_info` FROM `disc_posts` AS A INNER JOIN
 `6chan_users` AS B ON A.comment_by=B.user_id LEFT OUTER JOIN `6chan_users_dp` AS C
  ON B.user_id=C.user_id LEFT OUTER JOIN `6chan_posts_picture` AS D ON A.comment_id=D.post_id WHERE A.disc_id='{$pageid}'
   ORDER BY A.comment_id LIMIT $m,$n";

   $executeDataQuery=$db->query($sql);
   if ($db->error) {
     $reply="Cannot process the sql query ".$db->error;
     echo json_encode(['reply'=>false,'login'=>false,'res'=>$reply]);
     exit;
   }



$postInformation= array();
while ($a=$executeDataQuery->fetch_assoc()) {
  $postInformation[]=$a;
}


$sql="SELECT A.reply_id AS `reply_id`,A.reply AS `reply`,A.user_id AS `user_id`,
B.username AS `user_name`,A.post_id AS `post_id`,A.disc_id AS `disc_id` FROM
`6chan_posts_reply` AS A INNER JOIN `6chan_users` AS B ON A.user_id=B.user_id WHERE A.disc_id='{$pageid}';";

$replyquery=$db->query($sql);
if ($db->error) {
  $response=$db->error;
  echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
  exit;
}
$replySignal=true;
if ($replyquery->num_rows==0) {
  $replySignal=false;
  $replies='No replies';
}
else {
  $replies = array();
  while ($a=$replyquery->fetch_assoc()) {
    $replies[]=$a;
  }
  $replySignal=true;
}
if ($m==0) {
  $currentPage=1;
}
else {
  $currentPage=($m/$n)+1;
}


$sql="SELECT * FROM `6chan_discussions` WHERE `disc_id`='{$pageid}' AND `disc_created_by`='{$userId}';";
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
$executePostQuery->free();


$db->close();
echo json_encode(['reply'=>true,'postReply'=>true,'admin'=>$admin,'replySignal'=>$replySignal,
'replies'=>$replies,'pages'=>$pages,'currentPage'=>$currentPage,'disc_info'=>$information,
'userid'=>$userId,'post_info'=>$postInformation]);
exit;


}
else {
    echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
    exit;
}




 ?>
