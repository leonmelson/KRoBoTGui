<?php
function curlPost($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error !== '') {
        // echo $error;
    }

    return $response;
}

function ConnectToKRoBoT($DEBUGGING)
{
    $GetKRoBoTJson = GetKRoBoTJson();
    foreach($GetKRoBoTJson as $Key => $Value)
    {
        if($Value['primary']==TRUE)
        {
            $KRoBoT->KRoBoT = $Key;
            $ActiveKRoBoT=TRUE;
        }

    }
    if($ActiveKRoBoT==FALSE)
    {
        echo "<pre>";
        echo "NO ACTIVE ROBOT";
        echo "</pre>";
    }
    elseif($DEBUGGING==TRUE)
    {
        echo "<pre>";
        print_r($KRoBoT);
        echo "</pre>";
    }
    return $KRoBoT;
}
function GetKRoBoTJson()
{
    $url = 'http://192.168.0.52:5432/api';
    $get_help_data = ['id' => '123', 'method' => 'get_help'];
    $get_help = json_decode(curlPost($url, json_encode($get_help_data)), true);
    $GetROBOTJson = $get_help['robots'];
    // echo "<pre>";
    // print_r($GetROBOTJson);
    // echo "</pre>";
    return $GetROBOTJson;
}
function PushControl($PushPage)
{
    $pushpagedata = array();
    $AllowPushPage = 0;
    $currentTimeinSeconds = time();
   $GetPushControlData = json_decode(file_get_contents('tmp/pushcontrol.json'), true);
   if(!empty($GetPushControlData))
   {
        //$pushpagedata = $GetPushControlData;
        foreach($GetPushControlData as $PushData)
        {
            if($PushData['page']==$PushPage)
            {
//                if($currentTimeinSeconds-$PushData['time'] > 0.2)
//                {
                    $AllowPushPage = 1;
                    $pushpagedata[$PushPage]['page'] = $PushPage;
                    $pushpagedata[$PushPage]['time'] = $currentTimeinSeconds;
                    file_put_contents('tmp/pushcontrol.json', json_encode($pushpagedata));
//                }
            }
            else
            {
                if($currentTimeinSeconds-$PushData['time'] > 5)
                {
                    $AllowPushPage = 1;
                    $pushpagedata[$PushPage]['page'] = $PushPage;
                    $pushpagedata[$PushPage]['time'] = $currentTimeinSeconds;
                    file_put_contents('tmp/pushcontrol.json', json_encode($pushpagedata));
                }
            }
        }
        return($AllowPushPage);
   }
    else
    {
        $AllowPushPage = 1;
        $pushpagedata[$PushPage]['page'] = $PushPage;
        $pushpagedata[$PushPage]['time'] = $currentTimeinSeconds;
        file_put_contents('tmp/pushcontrol.json', json_encode($pushpagedata));
    }
}
function RequestFileData($FilePath)
{
    $result = file($FilePath);
    return $result;
}
function RequestConfigFileData($PathToROBOTCFG){
    $GetConfigJSONData = json_decode(file_get_contents($PathToROBOTCFG), true);
    $ConfigData = $GetConfigJSONData['DATA']['Return']; 
    return($ConfigData);
}
function RequestConfigGroupData($ConfigGroup, $PathToROBOTCFG){
    $ConfigFileData = RequestConfigFileData($PathToROBOTCFG);
    $ReturnGroupData = $ConfigFileData[$ConfigGroup];
    return($ReturnGroupData);
}
function RequestConfigGroupSingleData($ConfigGroupDATA, $GroupRow){
    foreach($ConfigGroupDATA as $configdata)
    {
        if($configdata[0] != '#')
        {
            $configdata = preg_replace('~[\r\n]+~', '', $configdata);
            if(strpos($configdata, $GroupRow) !== false)
            {
                $configData = explode(": ", $configdata);
            }
        }
    }
    if(empty($configData))
    {
        $configData = "OFF";
    }
    return($configData);
}
function UpdateConfigFileData($UpdateGroupSettingValueArray, $GroupOrSetting, $ROBOTCfg, $ROBOTUrl, $ROBOT)
{    
    $UpdateData['UpdateConfig']["ConfigData"] = $UpdateGroupSettingValueArray;
    $UpdateData['UpdateConfig']["GroupOrSetting"] = $GroupOrSetting;
    $UpdateData['UpdateConfig']["File"] = $ROBOTCfg;
    $UpdateData['KRoBoT'] = $ROBOT;
//    echo "<pre>";
//    print_r($UpdateData);
//    echo "<pre>";
    $ch = curl_init($ROBOTUrl);
    # Form data string
    $postString = http_build_query($UpdateData, '', '&');
    # Setting our options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    # Get the response
    $response = curl_exec($ch);
    curl_close($ch);

    print_r($response);
    return($response);
}
function GetUpdatedMaxValueForEndStopOffset($MaxValue, $MinValue, $EValue)
{
    if($MinValue[0] == '-')
    {
        if($EValue > $MinValue)
        {
           $NewMaxValue = $MaxValue + ($EValue-$MinValue);
        }
        elseif($EValue < $MinValue)
        {
           $NewMaxValue = $MaxValue - ($MinValue-$EValue);
        }
        else
        {
            $NewMaxValue = $MaxValue;
        }
    }
    else
    {
        if($EValue > $MinValue)
        {
           $NewMaxValue = $MaxValue + ($EValue-$MinValue);
        }
        elseif($EValue < $MinValue)
        {
           $NewMaxValue = $MaxValue - ($MinValue-$EValue);
        }
        else
        {
            $NewMaxValue = $MaxValue;
        }        
    }
    return($NewMaxValue);    
}
function GetFolderData($path)
{
    $GetFolderContent = scandir($path);
    foreach($GetFolderContent as $FolderContent)
    {
        if(is_dir($path."/".$FolderContent))
        {
            if($FolderContent!='.' AND $FolderContent!='..')
            {
                $FolderData['folders'][] = $FolderContent;
            }
        }
        else
        {
             $FolderData['files'][] = $FolderContent;
        }
    }
    return $FolderData;
}
function HomingData($PathToROBOTCFG)
{
    $HomingOverrideData = RequestConfigGroupData('homing_override', $PathToROBOTCFG);
    foreach($HomingOverrideData as $HomingData)
    {
        $HomingOData['homing_override'] = $HomingOverrideData;
        $HomingData = preg_replace('~[\r\n]+~', '', $HomingData);
        if(strpos($HomingData, 'gcode') !== false)
        {
            $HomingGcode = explode(": ", $HomingData);
            $HomingOData['gcode'] = $HomingGcode[1];
        }
        if(strpos($HomingData, 'axes') !== false)
        {
            $HomingAxes = explode(": ", $HomingData);
            $HomingOData['axes'] = $HomingAxes[1];
        }
    }
    return $HomingOData;
}