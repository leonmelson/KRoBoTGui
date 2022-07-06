<?php
include 'resources/functions.php';
include '/var/www/html/KRoBoT/resources/PHPClassKRoBoT.php';
//CONNECT TO ACTIVE ROBOT
$PathToROBOT = "/var/www/html/KRoBoT/";
$KRoBoT = ConnectToKRoBoT(0);
chdir("/var/www/html/KRoBoT");
$ROBOTTermJSON  = $KRoBoT->Url."&RequestJSONTerminalData=1";
//echo $KRoBoT->Url;
if (!$fp = curl_init($ROBOTTermJSON))
{
    exit;
}
$GetJsonTerminalDataAll = json_decode(file_get_contents($ROBOTTermJSON), true);
$GetJsonTerminalData = $GetJsonTerminalDataAll['DATA']['Return'];

$data = $_GET['data'];
if(!empty($data))
{
    date_default_timezone_set('Africa/Johannesburg');

    $starttimeex = explode(' ', microtime());
    $timestart = $starttimeex[1] + $starttimeex[0];
    $start = $timestart;

    $GetJsonTerminalDataAll = json_decode(file_get_contents($ROBOTTermJSON), true);
    $GetJsonTerminalData = $GetJsonTerminalDataAll['DATA']['Return'];
    // echo "<pre>";
    // print_r($GetJsonTerminalData);
    // echo "</pre>";
    $CountTerminalDataRows = count($GetJsonTerminalData['terminal']['sendcord']);
    //if($CountTerminalDataRows>=20)
    //{
    //    for($x = 0; $x <= $CountTerminalDataRows-20; $x++)
    //    {
    //        array_shift($GetJsonTerminalData['terminal']['data']);
    //    }
    //}
    
    for($x = 0; $x <= $CountTerminalDataRows-40; $x++)
    {        
        unset($GetJsonTerminalData['terminal']['sendcord'][$x]);
    }
    foreach($GetJsonTerminalData['terminal']['sendcord'] as $key => $value)
    {
        $GCommand = "";
        $valueNoSpace = "";
        $LineNumber = "";
        $SendCordCount = 0;
        $SXYZCords=array();
        $valueNoSpace = str_replace(" ", "", $value['Data']);
        $SendCordLineArray = str_split($valueNoSpace);
        //echo "<pre>";
        //print_r($SendCordLineArray);
        //echo "</pre>";
        foreach($SendCordLineArray as $SendCordCar)
        {
            if(!is_numeric($SendCordCar) AND $SendCordCar!="." AND $SendCordCar!="-")
            {
                $SXYZCords[$SendCordCount] = $SendCordCar;
                $SendCordActive = $SendCordCount;
                //echo $SendCordCar;
            }
            else
            {
               $SXYZCords[$SendCordActive] .= $SendCordCar; 
            }

            $SendCordCount=$SendCordCount+1;
        }
        //$SXYZCords = explode(" ", $value['Data']);
       //echo "<pre>";
       //print_r($SXYZCords);
       //echo "</pre>";
        foreach($SXYZCords as $SCords)
        {
            if($SCords[0]=="N")
            {
                $LineNumber = str_replace("N", "", $SCords);
            }
            if($SCords[0]=="G")
            {
                $GCommand = $SCords;
            }
            if(!empty($LineNumber) AND $GCommand != "")
            {
                if($SCords[0]=="X")
                {
                    $SCords = str_replace("X", "", $SCords);
                    $Moves[$LineNumber][$GCommand]['X'] = $SCords;
                }
                elseif($SCords[0]=="Y")
                {
                    $SCords = str_replace("Y", "", $SCords);
                    $Moves[$LineNumber][$GCommand]['Y'] = $SCords;
                }
                elseif($SCords[0]=="I")
                {
                    $SCords = str_replace("I", "", $SCords);
                    $Moves[$LineNumber][$GCommand]['I'] = $SCords;
                } 
                elseif($SCords[0]=="J")
                {
                    $SCords = str_replace("J", "", $SCords);
                    $Moves[$LineNumber][$GCommand]['J'] = $SCords;
                }                
            }
        }        
    }
//    echo "<pre>";
//    print_r($SXYZCords);
//    echo "</pre>";
    ksort($Moves);
    echo json_encode($Moves);
}
