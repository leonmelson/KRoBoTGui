<?php
date_default_timezone_set('Africa/Johannesburg');
include 'resources/functions.php';
include '/var/www/html/KRoBoT/resources/PHPClassKRoBoT.php';
//CONNECT TO ACTIVE ROBOT
$systemtemp = exec('/opt/vc/bin/vcgencmd measure_temp');
if (preg_match("/temp=(.*?)'C/", $systemtemp, $systemtempfilter) == 1) {
	$systemdata['temp'] = $systemtempfilter[1];
}
$systemcpu = exec("top -n 1 -b | awk '/^%Cpu/{print $2}'");
if(!empty($systemcpu))
{
    $systemdata['cpu'] = $systemcpu;
}
$ALLGETArgs = $_GET;
$AllPOSTArgs = $_POST;
$ZCords = "";
$PathToROBOT = "/var/www/html/KRoBoT/";
$KRoBoT = ConnectToKRoBoT(0);
chdir("/var/www/html/KRoBoT");
$ROBOTTermJSON  = $KRoBoT->Url."&RequestJSONTerminalData=1";
if (!$fp = curl_init($ROBOTTermJSON))
{
    exit;
}
$GetJsonTerminalDataAll = json_decode(file_get_contents($ROBOTTermJSON), true);
$GetJsonTerminalData = $GetJsonTerminalDataAll['DATA']['Return'];
// echo "<pre>";
// print_r($GetJsonTerminalData);
// echo "</pre>";
if(!is_array($GetJsonTerminalData))
{
    $Location['UPDATEVALUES']['RX'] = "0.000";
    $Location['UPDATEVALUES']['RY'] = "0.000";
    $Location['UPDATEVALUES']['RZ'] = "0.000";
    $Location['UPDATEVALUES']['SX'] = "0.000";
    $Location['UPDATEVALUES']['SY'] = "0.000";
    $Location['UPDATEVALUES']['SZ'] = "0.000";
    $Location['UPDATEVALUES']['SF'] = "0.000";
    $Location['UPDATEVALUES']['T'] = "0/0";
    $Location['UPDATEVALUES']['B'] = "0/0";
    //$Location['ENDSTOP']['a'] = "O";
    $Location['UPDATEVALUES']['FS'] = "0.0";
    $Location['UPDATEVALUES']['PSF'] = "1.0";
    $Location['UPDATEVALUES']['ESF'] = "1.0";    
    echo json_encode($Location);
}
else
{
    if(empty($GetJsonTerminalData['terminal']['receivecord']))
    {
        //$Location['UPDATEVALUES']['RX'] = "0.000";
        //$Location['UPDATEVALUES']['RY'] = "0.000";
        //$Location['UPDATEVALUES']['RZ'] = "0.000";         
    }
    else
    {
        foreach($GetJsonTerminalData['terminal']['receivecord'] as $receivecordline)
        {
            $XYZCords = explode(" ", $receivecordline['Data']);
            foreach($XYZCords as $Cords)
            {
                $Cord = explode(":", $Cords);
                if($Cord[0]=="X")
                {
                    $Location['UPDATEVALUES']['RX'] = round($Cord[1], 3);
                }
                if($Cord[0]=="Y")
                {
                    $Location['UPDATEVALUES']['RY'] = round($Cord[1], 3);
                }
                if($Cord[0]=="Z")
                {
                    $Location['UPDATEVALUES']['RZ'] = round($Cord[1], 3);
                    $ZCords = $Cord[1];
                }

            }            
        }
    }
    if(empty($GetJsonTerminalData['terminal']['gcodezoffset']))
    {
        //$Location['UPDATEVALUES']['GCOZ'] = "0.0";       
    }
    else
    {
        $gcodezoffset = $GetJsonTerminalData['terminal']['gcodezoffset'];
        $gcodezoffsetEx = explode("=", $gcodezoffset);
        $Location['UPDATEVALUES']['GCOZ'] = $gcodezoffsetEx[1];            
    }
    if(empty($GetJsonTerminalData['terminal']['sendcord']))
    {
        //$Location['UPDATEVALUES']['SX'] = "0.000";
        //$Location['UPDATEVALUES']['SY'] = "0.000";
        //$Location['UPDATEVALUES']['SZ'] = "0.000";
        //$Location['UPDATEVALUES']['SF'] = "0.000";        
    }
    else
    {
        $SendCordData = "";
        //$Location['UPDATEVALUES']['SX'] = "0.000";
        //$Location['UPDATEVALUES']['SY'] = "0.000";
        //$Location['UPDATEVALUES']['SZ'] = "0.000";
        //$Location['UPDATEVALUES']['SF'] = "0.000";  
        foreach($GetJsonTerminalData['terminal']['sendcord'] as $sendcordline)
        {
            $SendCordCount=0;
            $SXYZCords=array();
            $SendCordLineArray = str_split($sendcordline['Data']);
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
            //$SXYZCords = explode(" ", $sendcordline['Data']);
            foreach($SXYZCords as $SCords)
            {
                if($SCords[0]=="X")
                {
                    $SCords = str_replace("X", "", $SCords);
                    $Location['UPDATEVALUES']['SX'] = round($SCords, 3);
                }
                if($SCords[0]=="Y")
                {
                    $SCords = str_replace("Y", "", $SCords);
                    $Location['UPDATEVALUES']['SY'] = round($SCords, 3);
                }
                if($SCords[0]=="Z")
                {
                    $SCords = str_replace("Z", "", $SCords);
                    $Location['UPDATEVALUES']['SZ'] = round($SCords, 3);
                }
                if($SCords[0]=="F")
                {
                    $SCords = str_replace("F", "", $SCords);
                    $Location['UPDATEVALUES']['SF'] = round($SCords);
                }
            }
            
        }
    }
    if(empty($GetJsonTerminalData['terminal']['receiveendstop']))
    {
        //$Location['ENDSTOP']['EMPTY'] = "0";       
    }
    else
    {
        $receivedendstop = $GetJsonTerminalData['terminal']['receiveendstop'];
        $EndStopValues = explode(" ", $receivedendstop);
        foreach($EndStopValues as $EndStopValue)
        {
            $EndStopData = explode(":", $EndStopValue);
            if($EndStopData[1]=="open")
            {
                $Location['ENDSTOP'][$EndStopData[0]] = "O";
            }
            else
            {
                $Location['ENDSTOP'][$EndStopData[0]] = "T";
            }
            $EndStopArray[strtoupper($EndStopData[0])] = $EValue;
        }
    }
    if(empty($GetJsonTerminalData['terminal']['receivetemp']))
    {
        //$Location['UPDATEVALUES']['T'] = "T0: 0/0";       
    }
    else
    {
        $receivedtemp = $GetJsonTerminalData['terminal']['receivetemp'];
        $TempValues = str_replace(" /", "/", $receivedtemp);
        $TempValues = str_replace("ok ", "", $TempValues);
        $TempValues = explode(" ", $TempValues);
        foreach($TempValues as $TempValue)
        {
            $Location['UPDATEVALUES'][$TempValue[0]] = $TempValue;            
        }
    }
    if(empty($systemdata['temp']))
    {
        //$Location['UPDATEVALUES']['ST'] = "PI=0";       
    }
    else
    {
        $Location['UPDATEVALUES']['ST'] = "πT:".$systemdata['temp']."/C:".$systemdata['cpu']; 
    }
    
    if(empty($GetJsonTerminalData['terminal']['speedfactor']))
    {
        //$Location['UPDATEVALUES']['PSF'] = "1.0";       
    }
    else
    {
        $printspeedfactor = $GetJsonTerminalData['terminal']['speedfactor'];
        $printspeedfactorEx = explode("=", $printspeedfactor);
        $Location['UPDATEVALUES']['PSF'] = $printspeedfactorEx[1];            
    }    
    if(empty($GetJsonTerminalData['terminal']['espeedfactor']))
    {
        //$Location['UPDATEVALUES']['ESF'] = "1.0";       
    }
    else
    {
        $extrudespeedfactor = $GetJsonTerminalData['terminal']['espeedfactor'];
        $extrudespeedfactorEx = explode("=", $extrudespeedfactor);
        $Location['UPDATEVALUES']['ESF'] = $extrudespeedfactorEx[1];            
    }
    if(empty($GetJsonTerminalData['terminal']['fanspeed']))
    {
        //$Location['UPDATEVALUES']['ESF'] = "1.0";       
    }
    else
    {
        $fanspeed = $GetJsonTerminalData['terminal']['fanspeed'];
        $fanspeedEx = explode("=", $fanspeed);
        $Location['UPDATEVALUES']['FS'] = $fanspeedEx[1];            
    }
    if(!empty($GetJsonTerminalData['terminal']['receiveprobe']))
    {
        $Location['PROBE'] = $GetJsonTerminalData['terminal']['receiveprobe'];     
    }
    if(!empty($GetJsonTerminalData['terminal']['progress']))
    {
        $Location['PROGRESS'] = $GetJsonTerminalData['terminal']['progress'];     
    }    
    //print_r($Location);
    echo json_encode($Location);
}

