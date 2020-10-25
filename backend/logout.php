<?php

if (isset($_COOKIE['user_id'])&&isset($_COOKIE['security'])) {
  setcookie("user_id","",time()-1);
  setcookie("security","",time()-1);
  $response="Logout Successful";
  echo json_encode($response);
}


 ?>
