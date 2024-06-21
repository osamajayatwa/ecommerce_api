<?php

include "../connect.php";

$email - filterRequest("email");
$password = sha1($_POST['password']);
$data = array ("users_pass" => $password );
updateData("users" , $data , "users_email = '$email'" );