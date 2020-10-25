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

$pageid=$_POST['pageId'];

$sql="SELECT A.disc_id AS `disc_id`,A.disc_name AS `disc_name`,A.disc_category
 AS `disc_category`,B.username AS `user_name`, A.disc_created_on AS `disc_created_on`,
 A.disc_members AS `disc_members`,A.disc_description AS `disc_description`,
 (SELECT COUNT(comment_id) FROM `disc_posts` WHERE disc_posts.disc_id='{$pageid}')
  AS `disc_comments` FROM `6chan_discussions` AS A INNER JOIN `6chan_users` AS B
  ON A.disc_created_by=B.user_id WHERE `disc_id`='{$pageid}';";

  $details=$db->query($sql);
  if ($db->error) {
    $response=$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  $info=$details->fetch_assoc();

  $details->free();
  $db->close();

  echo json_encode(['reply'=>true,'info'=>$info]);
  exit;

}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
