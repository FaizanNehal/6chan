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

  $discId=$_POST['discId'];
  $removePost=$_POST['removePost'];

  $sql="DELETE FROM `disc_posts` WHERE `comment_id`='{$removePost}' AND `disc_id`='{$discId}';";
  $removing=$db->query($sql);
  if ($db->error) {
    $response="Cannot Execute the remove user query";
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }


   $sql="SELECT A.pic_id,A.post_id,B.disc_id,A.picture_info FROM `6chan_posts_picture`
    AS A INNER JOIN `disc_posts` AS B ON A.post_id=B.comment_id WHERE A.post_id='{$removePost}';";

    $pictureinfo=$db->query($sql);
    if ($db->error) {
      $response=$db->error;
      echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
      exit;
    }
    if ($pictureinfo->num_rows==0) {

    }
    $information=$pictureinfo->fetch_assoc();
    $location=$information['picture_info'];
    $path='./../assets/posts/'.$discId.'/'.$location;
    $location=unlink($path);

  $sql="DELETE FROM `6chan_posts_picture` WHERE `post_id`='{$removePost}';";
  $removePicture=$db->query($sql);
  if ($db->error) {
    $response=$db->error;
    echo json_encode(['reply'=>false,'login'=>false,'res'=>$response]);
    exit;
  }
  $db->close();

  $response="Post removed successfully";
   echo json_encode(['reply'=>true,'res'=>$response]);
   exit;


}
else {
  echo json_encode(['reply'=>false,'login'=>true,'res'=>'Please Login first']);
  exit;

}


 ?>
