<?php
date_default_timezone_set('Africa/Johannesburg');
include 'resources/functions.php';
include '/var/www/html/KRoBoT/resources/PHPClassKRoBoT.php';
//CONNECT TO ACTIVE ROBOT
$PathToROBOT = "/var/www/html/KRoBoT";
$KRoBoT = ConnectToKRoBoT(0);

//VARS
//Get Push Control to allow lower instances

$PushPage = $_GET['page'];
if(!empty($PushPage))
{
    $GetPushAllowed = PushControl($PushPage);
    if($GetPushAllowed!=1)
    {
        exit;
    }
}
else
{    
    exit;
}
$KRoBoT->Push("Single", "M114");
usleep(50000);
$KRoBoT->Push("Single", "M105");
?>