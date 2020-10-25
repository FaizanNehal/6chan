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


  $discId=$_POST['disc_id'];
  $comment=$_POST['comment'];
  $date=date("Y-m-d");

  $sql="SELECT * FROM `discussion_users` WHERE `user_id`='{$userId}' AND `disc_id`='{$discId}'";
  $verify=$db->query($sql);

  if ($db->error) {
    $response="Cannot process the query";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  if ($verify->num_rows==0) {
    $response="You have not joined this forum. First join in order to post a comment";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }




  $sql="INSERT INTO `disc_posts` SET `comment`='{$comment}',`comment_by`='{$userId}',`disc_id`='{$discId}',
  `date_posted`='{$date}';";

  $insertComment=$db->query($sql);
  if($db->error){
    $response="Cannot process the inserting query";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }

  if ($_FILES['image']) {

    $postId=$db->insert_id;

    $imgName=$_FILES['image']['name'];
    $imgNameBreak=explode('.',$imgName);
    $imgName=array_pop($imgNameBreak);

    $imgName=$postId.'.'.$imgName;
    $_FILES['image']['name']=$imgName;
    $path="./../assets/posts/".$discId;
    if (!file_exists($path)) {
      mkdir($path);
      move_uploaded_file($_FILES['image']['tmp_name'],$path.'/'.$_FILES['image']['name']);
    }
    else {
      move_uploaded_file($_FILES['image']['tmp_name'],$path.'/'.$_FILES['image']['name']);
    }
    $sql="INSERT INTO `6chan_posts_picture` SET `post_id`='{$postId}',`picture_info`='{$imgName}';";
    $insertPicture=$db->query($sql);
    if ($db->error) {
      $response=$db->error;
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    echo json_encode(['reply'=>true,'res'=>'inserted']);
    exit;


  }

  echo json_encode(['reply'=>true,'res'=>'inserted']);
  exit;


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
