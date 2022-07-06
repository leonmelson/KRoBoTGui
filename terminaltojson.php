<?php
date_default_timezone_set('Africa/Johannesburg');
include 'resources/functions.php' ;
include '/var/www/html/ROBOT/resources/PHPClassROBOT.php';
//CONNECT TO ACTIVE ROBOT
$PathToROBOT = "/var/www/html/ROBOT/";
$ROBOT = ConnectToROBOT(0);

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
$DataRowCount = 60;
$GetJsonTerminalData = json_decode(file_get_contents('tmp/terminal.json'), true);
////echo "<pre>";
////print_r($GetJsonTerminalData);
////echo "</pre>";

$ROBOTTerm = $ROBOT->SPort.".term";
//echo $ROBOTTerm;
$lines = $ROBOT->RequestLastTermData($ROBOTTerm);
//print_r($lines);
//exec("truncate -s 0 ".$ROBOTTerm);

//echo "<pre>";
//print_r($SendLog);
//echo "</pre>";
//echo "<pre>";
//print_r($RecvLog);
//echo "</pre>";
////sort($lines);
////    for($x = 0; $x <= count($lines)-20; $x++)
////    {        
////        unset($lines[$x]);
////    }
//array_pop($lines);
//echo "<pre>";
//print_r($lines);
//echo "</pre>";
foreach($lines as $tline)
{
    if($tline!="")
    {
        $TerminalAllData = explode(" - ", $tline);
        $TerminalDataLine = explode("*", $TerminalAllData[1]);
        $TerminalDataLine[0] = str_replace("\n", '', $TerminalDataLine[0]);
        if($TerminalDataLine[0]!="")
        {
            $GetJsonTerminalData['terminal']['data'][]['Data'] = $TerminalDataLine[0];

            $TerminalDataBoth = explode(": ", $TerminalDataLine[0]);
            if($TerminalDataBoth[0]=="Send")
            {
                if(strpos($TerminalDataBoth[1], 'G1') !== false OR strpos($TerminalDataBoth[1], 'G0') !== false)
                {
                    if(strpos($TerminalDataBoth[1], 'X') !== false OR strpos($TerminalDataBoth[1], 'Y') !== false OR strpos($TerminalDataBoth[1], 'Z') !== false OR strpos($TerminalDataBoth[1], 'F') !== false)
                    {
                        $GetJsonTerminalData['terminal']['sendcord'][]['Data'] = $TerminalDataBoth[1];
                    }
                }
                $GetJsonTerminalData['terminal']['send'][]['Data'] = $TerminalDataBoth[1];
            }
            if($TerminalDataBoth[0]=="Recv")
            {
                if(substr($TerminalDataBoth[1], 0, 1) == 'x' OR substr($TerminalDataBoth[1], 0, 1) == 'y' OR substr($TerminalDataBoth[1], 0, 1) == 'a' OR substr($TerminalDataBoth[1], 0, 1) == 'b' OR substr($TerminalDataBoth[1], 0, 1) == 'z' OR substr($TerminalDataBoth[1], 0, 1) == 'c')
                {
                    $GetJsonTerminalData['terminal']['receiveendstop'][]['Data'] = $TerminalDataBoth[1];
                }

                if(substr($TerminalDataBoth[1], 0, 1) == 'X' OR substr($TerminalDataBoth[1], 0, 1) == 'Y' OR substr($TerminalDataBoth[1], 0, 1) == 'Z' OR substr($TerminalDataBoth[1], 0, 1) == 'F')
                {
                    $GetJsonTerminalData['terminal']['receivecord'][]['Data'] = $TerminalDataBoth[1];
                }
                $GetJsonTerminalData['terminal']['receive'][]['Data'] = $TerminalDataBoth[1];
            }
        }
    }
}
ksort($GetJsonTerminalData['terminal']['data']);
foreach($GetJsonTerminalData['terminal']['data'] as $DataArrayShift)
{
    if($DataCount=="")
        $DataCount=0;
    if($DataCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['data']);
    }
    $DataCount = $DataCount + 1;
}
ksort($GetJsonTerminalData['terminal']['send']);
foreach($GetJsonTerminalData['terminal']['send'] as $SendArrayShift)
{
    if($SendCount=="")
        $SendCount=0;
    if($SendCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['send']);
    }
    $SendCount = $SendCount + 1;
}
ksort($GetJsonTerminalData['terminal']['receive']);
foreach($GetJsonTerminalData['terminal']['receive'] as $ReceiveArrayShift)
{
    if($ReceiveCount=="")
        $ReceiveCount=0;        
    if($ReceiveCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['receive']);
    }
    $ReceiveCount = $ReceiveCount + 1;
}
ksort($GetJsonTerminalData['terminal']['sendcord']);
foreach($GetJsonTerminalData['terminal']['sendcord'] as $SendCordArrayShift)
{
    if($SendCordCount=="")
        $SendCordCount=0;
    if($SendCordCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['sendcord']);
    }
    $SendCordCount = $SendCordCount + 1;
}
ksort($GetJsonTerminalData['terminal']['receivecord']);
foreach($GetJsonTerminalData['terminal']['receivecord'] as $ReceiveCordArrayShift)
{
    if($ReceiveCordCount=="")
        $ReceiveCordCount=0;    
    if($ReceiveCordCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['receivecord']);
    }
    $ReceiveCordCount = $ReceiveCordCount + 1;
}
ksort($GetJsonTerminalData['terminal']['receiveendstop']);
foreach($GetJsonTerminalData['terminal']['receiveendstop'] as $ReceiveEndStopArrayShift)
{
    if($ReceiveEndStopCount=="")
        $ReceiveEndStopCount=0;    
    if($ReceiveEndStopCount>=$DataRowCount)
    {
        array_shift($GetJsonTerminalData['terminal']['receiveendstop']);
    }
    $ReceiveEndStopCount = $ReceiveEndStopCount + 1;
}

$CountTerminalDataRows = count($GetJsonTerminalData['terminal']['data']);
$CountTerminalReceiveRows = count($GetJsonTerminalData['terminal']['receive']);
$CountTerminalSendRows = count($GetJsonTerminalData['terminal']['send']);
$CountTerminalSendCordRows = count($GetJsonTerminalData['terminal']['sendcord']);
$CountTerminalReceiveCordRows = count($GetJsonTerminalData['terminal']['receivecord']);
$CountTerminalReceiveEndStop = count($GetJsonTerminalData['terminal']['receiveendstop']);

echo $CountTerminalDataRows." ".$CountTerminalReceiveRows." ".$CountTerminalSendRows." ".$CountTerminalSendCordRows." ".$CountTerminalReceiveCordRows;

echo "<pre>";
print_r($GetJsonTerminalData);
echo "</pre>";

$json_data = json_encode($GetJsonTerminalData);
file_put_contents("tmp/terminal.json", $json_data);