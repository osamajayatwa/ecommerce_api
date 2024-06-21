<?php
include "../../connect.php";

$username = filterRequest("username");
$password = sha1($_POST['password']);
$email = filterRequest("email");
$phone = filterRequest("phone");
$verfiycode     = rand(10000 , 99999);

$stmt = $con->prepare("SELECT * FROM admins WHERE admins_email = ? OR admins_phone = ? ");
$stmt->execute(array($email, $phone));
$count = $stmt->rowCount();
if ($count > 0) {
    printFailure("PHONE OR EMAIL");
} else {

    $data = array(
        "admins_name" => $username,
        "admins_email" => $email,
        "admins_password" => $password,
        "admins_phone" => $phone,
        "admins_verfiycode" => $verfiycode,
    );
    // sendEmail($email , "Verfiy Code Ecommerce" , "Verfiy Code $verfiycode"); 
    insertData("admins" , $data) ; 

}
