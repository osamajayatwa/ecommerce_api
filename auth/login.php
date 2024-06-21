<?php
include "../connect.php";
$email = filterRequest("email");
$password = sha1($_POST['password']);

getData("users" , "users_email = ? AND users_password = ? AND users_approve = 1" ,  array($email , $password));


