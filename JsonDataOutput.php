<?php
date_default_timezone_set('Africa/Johannesburg');
header("Content-Type: application");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include 'resources/functions.php';
include '/var/www/html/KRoBoT/resources/PHPClassKRoBoT.php';
//CONNECT TO ACTIVE ROBOT
$PathToROBOT = "/var/www/html/KRoBoT/";
$KRoBoT = ConnectToKRoBoT(0);
chdir("/var/www/html/KRoBoT");
$ROBOTTermJSON  = $KRoBoT->Url."&RequestJSONTerminalData=1";
if (!$fp = curl_init($ROBOTTermJSON))
{
    exit;
}


$filemanagerpath = filter_input(INPUT_GET, 'filemanagerpath');
$data = filter_input(INPUT_GET, 'data');
$raw = filter_input(INPUT_GET, 'raw');
if(empty($raw))
{
    $StringCount=100;
    $RowCount=20;
}
elseif($raw=="display")
{
    $StringCount=400;
    $RowCount=100;
}
    

if(!empty($data))
{
    date_default_timezone_set('Africa/Johannesburg');
    $CountTerminalDataRows = "";

    $GetJsonTerminalDataAll = json_decode(file_get_contents($ROBOTTermJSON), true);
    $GetJsonTerminalData = $GetJsonTerminalDataAll['DATA']['Return'];
    //echo "<pre>";
    //print_r($GetJsonTerminalData);
    //echo "</pre>";
    $CountTerminalDataRows = count($GetJsonTerminalData['terminal'][$data]);
    //if($CountTerminalDataRows>=20)
    //{
    //    for($x = 0; $x <= $CountTerminalDataRows-20; $x++)
    //    {
    //        array_shift($GetJsonTerminalData['terminal']['data']);
    //    }
    //}
    for($x = 0; $x <= $CountTerminalDataRows-$RowCount; $x++)
    {
        array_shift($GetJsonTerminalData['terminal'][$data]);
    }
    foreach($GetJsonTerminalData['terminal'][$data] as $key => $value)
    {
        if (strlen($value['Data']) > $StringCount)
        $GetJsonTerminalData['terminal'][$data][$key]['Data'] = substr($value['Data'], 0, $StringCount);
    }
    if(count($GetJsonTerminalData['terminal'][$data])<=$RowCount)
    {
        if($raw=="display")
        {
            foreach($GetJsonTerminalData['terminal'][$data] as $DisplayData)
            {
                echo $DisplayData['Data']."\n";
            } 
        }
        else
        {
            echo json_encode($GetJsonTerminalData['terminal'][$data]);
        }
    }
}
if(!empty($filemanagerpath))
{
    $GetFolderContent = GetFolderData($filemanagerpath);
    if(!empty($GetFolderContent))
    {
        echo json_encode($GetFolderContent);
    }
}
