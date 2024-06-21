<?php
include "../../connect.php";
$email = filterRequest("email"); 
$password = sha1($_POST['password']);

getData("admins" , "admins_email = ? AND admins_password = ? AND admins_approve = 1" ,  array($email , $password));
 
