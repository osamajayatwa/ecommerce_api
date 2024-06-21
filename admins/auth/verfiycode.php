<?php 

include "../../connect.php" ;

$email  = filterRequest("email") ; 

$verfiy = filterRequest("verifycode") ; 

$stmt = $con->prepare("SELECT * FROM `admins` WHERE admins_email = '$email' AND admins_verfiycode = '$verfiy' ") ; 
 
$stmt->execute() ; 

$count = $stmt->rowCount() ; 

if ($count > 0) {
 
    $data = array("admins_approve" => "1") ; 

    updateData("admins" , $data , "admins_email = '$email'");

}else {
    
 printFailure("verifycode not Correct") ; 

}
