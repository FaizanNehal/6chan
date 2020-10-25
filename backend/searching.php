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


  $searchWord=$_POST['search'];
  $searchWord=addslashes($searchWord);
  if ($_POST['users']=='set') {
    $sql="SELECT A.user_id AS `user_id`,A.username AS `username`,A.user_fname AS `first_name`
    ,A.user_lname AS `last_name`,B.picture_info AS `picture`,(SELECT COUNT(comment_id) FROM
     `disc_posts` WHERE disc_posts.comment_by=A.user_id) AS `comments` FROM `6chan_users` AS A
      LEFT OUTER JOIN `6chan_users_dp` AS B ON A.user_id=B.user_id WHERE (`username` LIKE '%{$searchWord}%')
       OR (`user_fname` LIKE '%{$searchWord}%') OR (`user_lname` LIKE '%{$searchWord}%');";

    $userresult=$db->query($sql);
    if ($db->error) {
      $response="Cannot execute the query  ".$db->error;
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }

    if ($result->num_rows==0) {
      $response="Cannot find any users with this name";
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    $userresult_break=array();
    while($a=$userresult->fetch_assoc()){
      $userresult_break[]=$a;
    }

    echo json_encode(['reply'=>true,'user'=>true,'forum'=>false,'users'=>$userresult_break]);
    exit;

  }

  $sql="SELECT `disc_id`,`disc_name`,`disc_category`,(SELECT COUNT(comment_id)
  FROM `disc_posts` WHERE disc_posts.disc_id=6chan_discussions.disc_id) AS
  `disc_comments` FROM `6chan_discussions` WHERE `disc_name` LIKE '%{$searchWord}%';";

  $result=$db->query($sql);
  if ($db->error) {
    $response="Cannot execute the query  ".$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  if ($result->num_rows==0) {
    $response="Cannot find any forum with this name";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  $result_break=array();
  while($a=$result->fetch_assoc()){
    $result_break[]=$a;
  }

  echo json_encode(['reply'=>true,'user'=>false,'forum'=>true,'forums'=>$result_break]);
  exit;

}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}
 ?>
