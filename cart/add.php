<?php
include "../connect.php";


$usersid = filterRequest("usersid");
$itemsid = filterRequest("itemsid");


  getData("cart", "cart_usersid = $usersid AND cart_itemsid = $itemsid AND  cart_orders = 0" , null , false );


$data = array(
    "cart_usersid" => $usersid,
    "cart_itemsid" => $itemsid
);

insertData("cart", $data);
 

  