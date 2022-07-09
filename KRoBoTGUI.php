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

$ALLGETArgs = $_GET;
$AllPOSTArgs = $_POST;

if(!empty($ALLGETArgs['ConfigClean']) OR !empty($AllPOSTArgs['ConfigClean']))
{
    $PathToROBOTCFG = $KRoBoT->Url."&ReadConfig=1";
}
else
{
    $PathToROBOTCFG = $KRoBoT->Url."&ReadConfig=2";
}
$ConfigFileData = RequestConfigFileData($PathToROBOTCFG);
//echo "<pre>";
//print_r($ConfigFileData);
//echo "</pre>";
$KRoBoTUrl = $KRoBoT->Url; 
$KRoBoTCfg = "cfg/".$KRoBoT->KRoBoT.".cfg";
$BLTouch = 0;
$UpdateMeshMinXOffset = filter_input(INPUT_POST, 'meshminxoffset');
$UpdateMeshMinYOffset = filter_input(INPUT_POST, 'meshminyoffset');
$UpdateMeshMaxXOffset = filter_input(INPUT_POST, 'meshmaxxoffset');
$UpdateMeshMaxYOffset = filter_input(INPUT_POST, 'meshmaxyoffset');
$UpdateMeshProbeXCount = filter_input(INPUT_POST, 'meshprobexcount');
$UpdateMeshProbeYCount = filter_input(INPUT_POST, 'meshprobeycount');
$UpdateMeshLevelingSpeed = filter_input(INPUT_POST, 'meshlevelingspeed');
$UpdateProbeSpeed = filter_input(INPUT_POST, 'probespeed');
$BLTouchEnabled = filter_input(INPUT_POST, 'BLTouch');

$UpdateXEnstopOffset = filter_input(INPUT_POST, 'xendstopoffset');
$UpdateYEnstopOffset = filter_input(INPUT_POST, 'yendstopoffset');
$UpdateZEnstopOffset = filter_input(INPUT_POST, 'zendstopoffset');
$UpdateYPosMax = filter_input(INPUT_POST, 'yposmax');
$UpdateYPosMin = filter_input(INPUT_POST, 'yposmin');
$UpdateXPosMax = filter_input(INPUT_POST, 'xposmax');
$UpdateXPosMin = filter_input(INPUT_POST, 'xposmin');
$UpdateHomeXY = filter_input(INPUT_POST, 'homexy');
$UpdateHomeZ = filter_input(INPUT_POST, 'homez');
$UpdateHomeXYZ = filter_input(INPUT_POST, 'homexyz');
$UpdateNoHome = filter_input(INPUT_POST, 'nohome');
//echo "<pre>";
//print_r($_GET);
//echo "</pre>";
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
if(isset($UpdateMeshMaxXOffset) AND isset($UpdateMeshMaxYOffset) AND isset($UpdateMeshMinXOffset) AND isset($UpdateMeshMinYOffset) AND isset($UpdateMeshProbeXCount) AND isset($UpdateMeshProbeYCount) AND isset($UpdateMeshLevelingSpeed) AND isset($UpdateProbeSpeed))
{
    $MinUpdateMeshValue = $UpdateMeshMinXOffset.", ".$UpdateMeshMinYOffset;
    $MaxUpdateMeshValue = $UpdateMeshMaxXOffset.", ".$UpdateMeshMaxYOffset;
    $ProbeCountUpdateMeshValue = $UpdateMeshProbeXCount.", ".$UpdateMeshProbeYCount;
    $UpdateMeshArray['bed_mesh']['mesh_min'] = $MinUpdateMeshValue;
    $UpdateMeshArray['bed_mesh']['mesh_max'] = $MaxUpdateMeshValue;
    $UpdateMeshArray['bed_mesh']['probe_count'] = $ProbeCountUpdateMeshValue;
    $UpdateMeshArray['bed_mesh']['speed'] = $UpdateMeshLevelingSpeed;
    if($BLTouchEnabled==0)
    {
        $UpdateMeshArray['probe']['speed'] = $UpdateProbeSpeed;
    }
    else
    {
        $UpdateMeshArray['bltouch']['speed'] = $UpdateProbeSpeed;
    }
    UpdateConfigFileData($UpdateMeshArray, "Setting", $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    $KRoBoT->Push("Single", "RESTART", 1);
}
if(isset($UpdateXEnstopOffset) AND isset($UpdateYEnstopOffset) AND isset($UpdateYPosMax) AND isset($UpdateYPosMin) AND isset($UpdateXPosMax) AND isset($UpdateXPosMin))
{

    $UpdateEndstopArray['stepper_x']['position_endstop'] = $UpdateXEnstopOffset;
    $UpdateEndstopArray['stepper_y']['position_endstop'] = $UpdateYEnstopOffset;
    $UpdateEndstopArray['probe']['z_offset'] = $UpdateZEnstopOffset;
    $UpdateEndstopArray['stepper_x']['position_max'] = $UpdateXPosMax;
    $UpdateEndstopArray['stepper_y']['position_min'] = $UpdateYPosMin;
    $UpdateEndstopArray['stepper_y']['position_max'] = $UpdateYPosMax;
    $UpdateEndstopArray['stepper_x']['position_min'] = $UpdateXPosMin;
    // echo "<pre>";
    // print_r($UpdateEndstopArray);
    // echo "</pre>";
    UpdateConfigFileData($UpdateEndstopArray, 'Setting', $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    $KRoBoT->Push("Single", "RESTART", 1);
}
if(isset($UpdateHomeXY) OR isset($UpdateHomeZ) OR isset($UpdateHomeXYZ) or isset($UpdateNoHome))
{
    if($UpdateHomeXYZ!="on")
    {
        $AddRemoveGroup = "Group";
        if($UpdateHomeXY=="on" AND $UpdateHomeZ=="on")
        {
            $UpdateHomingOverride['homing_override']['gcode'] = "G28 Z0\n SAVE_GCODE_STATE NAME=ProbeZ\n G91\n G1 Z10\n RESTORE_GCODE_STATE NAME=ProbeZ";
            $UpdateHomingOverride['homing_override']['axes'] = "z";
            $UpdateHomingOverride['homing_override']['set_position_z'] = "0";
        }
        elseif($UpdateHomeXY=="on")
        {
            $UpdateHomingOverride['homing_override']['gcode'] = "";
            $UpdateHomingOverride['homing_override']['axes'] = "z";
            $UpdateHomingOverride['homing_override']['set_position_z'] = "0\n";
        }
        elseif($UpdateHomeZ=="on")
        {
            $UpdateHomingOverride['homing_override']['gcode'] = "G28 Z0\n SAVE_GCODE_STATE NAME=ProbeZ\n G91\n G1 Z10\n RESTORE_GCODE_STATE NAME=ProbeZ";
            $UpdateHomingOverride['homing_override']['axes'] = "xyz";
            $UpdateHomingOverride['homing_override']['set_position_x'] = "0";
            $UpdateHomingOverride['homing_override']['set_position_y'] = "0";
            $UpdateHomingOverride['homing_override']['set_position_z'] = "0\n";
        }
        else
        {
            $UpdateHomingOverride['homing_override']['gcode'] = "";
            $UpdateHomingOverride['homing_override']['axes'] = "xyz";
            $UpdateHomingOverride['homing_override']['set_position_x'] = "0";
            $UpdateHomingOverride['homing_override']['set_position_y'] = "0";
            $UpdateHomingOverride['homing_override']['set_position_z'] = "0\n";
        }
    }
    else
    {
        $AddRemoveGroup = "RemoveGroup";
        $UpdateHomingOverride['homing_override'] = "";
    }
    UpdateConfigFileData($UpdateHomingOverride, $AddRemoveGroup, $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    //$KRoBoT->RestartPushControl("commands");
    $KRoBoT->Push("Single", "RESTART", 1);
}
$emptysaveconfig = filter_input(INPUT_GET, 'emptysaveconfig');
$emptysaveconfigverify = filter_input(INPUT_GET, 'emptysaveconfigverify');
if($emptysaveconfigverify==1)
{
    $EmptySaveConfig['SAVE_CONFIG'] = 1;
    UpdateConfigFileData($EmptySaveConfig, 'RemoveGroup', $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    //$KRoBoT->RestartPushControl("commands");
    $KRoBoT->Push("Single", "RESTART", 0);
    header('refresh: 1; url=KRoBoTGUI.php?State=config');
}
if(!empty($AllPOSTArgs["CFGHeading"]) AND !empty($AllPOSTArgs["CFGOption"]) AND !empty($AllPOSTArgs["CFGOptionEdit"]))
{
    $UpdateOptionArray[$AllPOSTArgs["CFGHeading"]][$AllPOSTArgs["CFGOption"]] = $AllPOSTArgs["CFGOptionValue"];
    UpdateConfigFileData($UpdateOptionArray, "Setting", $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    $KRoBoT->Push("Single", "RESTART", 1);
}
if(!empty($AllPOSTArgs["CFGEDITED"]))
{
//    echo "<pre>";
//    print_r($AllPOSTArgs["CFGEDITED"]);
//    echo "</pre>";
    UpdateConfigFileData($AllPOSTArgs["CFGEDITED"], "ALL", $KRoBoTCfg, $KRoBoTUrl, $KRoBoT->KRoBoT);
    $KRoBoT->Push("Single", "RESTART", 1);
}
//Load Work File
$DefaultUploadPath = '/var/www/html/KRoBoT/uploads';
$SentPath = filter_input(INPUT_GET, 'sentpath');
$LoadFile = filter_input(INPUT_GET, 'loadfile');
$StartFile = filter_input(INPUT_GET, "StartFile");

if(!empty($LoadFile))
{
    $RequestedAction = "LoadFile";

}
if(!empty($StartFile))
{
    if(!empty($SentPath))
    {
       $PathToFile = $DefaultUploadPath.$SentPath."/".$StartFile; 
    }
    else
    {
       $PathToFile = $DefaultUploadPath."/".$StartFile; 
    }
   $KRoBoTPushFile = $KRoBoT->Push("File", $PathToFile, 1);
//    echo "<pre>";
//    echo $PathToFile;
//    echo "</pre>";
//    echo "<pre>";
//    print_r($SendCommand);
//    echo "</pre>";
    usleep(200000);
}

$emptyterminal = filter_input(INPUT_GET, 'emptyterminal');

if(!empty($emptyterminal))
{
    echo "<pre>";
    echo $KRoBoT->ClearTerminalData();
    echo "</pre>";
}
$PageState = $ALLGETArgs['State'];
if(empty($PageState))
{
    $PageState = "control";
}
?>
<html>
    <head>
        <title>
        KRoBoT
        </title>
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="resources/css.css">
        <script type="text/javascript" src="resources/jquery.min.js">            
        </script>
        <script type="text/javascript" src="resources/java.js">
        </script>
    </head>
    <body>
        <?php
        if(!empty($RequestedAction))
        {
            ?>
            <div class="RequistedAction">
                <?php
                if($RequestedAction=="LoadFile")
                {
                    ?>
                    <a href="?State=control&sentpath=<?php echo $SentPath;?>&StartFile=<?php echo $LoadFile;?>">START FILE <?php echo $LoadFile;?></a>
                    <?php
                }
                ?>
            </div>
            <?php           
        }
        ?>
        <div class="BusyBox">
            <div id="ESTOP" data-commands="M112" data-command-type="emergency" data-coms-type="uds">
                ESTOP
            </div>
            <div class="BusyBoxControlBox">
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        PRINT SPEED
                    </div>

                    <div class="BusyBoxControlContent" id="busypsf">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busypsfplus">
                            +
                        </div>
                        <div class="busypsfmin">
                            -
                        </div>
                    </div>
                </div>
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        EXTRUDE F
                    </div>
                    <div class="BusyBoxControlContent" id="busyesf">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busyesfplus">
                            +
                        </div>
                        <div class="busyesfmin">
                            -
                        </div>
                    </div>
                </div>
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        FAN SPEED
                    </div>
                    <div class="BusyBoxControlContent" id="busyfs">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busyfsplus">
                            +
                        </div>
                        <div class="busyfsmin">
                            -
                        </div>
                    </div>
                </div>
            </div>
            <div class="BusyBoxControlBox">
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        BED TEMP
                    </div>
                    <div class="BusyBoxControlContent" id="busybt">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busybtplus">
                            +
                        </div>
                        <div class="busybtmin">
                            -
                        </div>
                    </div>
                </div>
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        HOTEND TEMP
                    </div>
                    <div class="BusyBoxControlContent" id="busyet">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busyetplus">
                            +
                        </div>
                        <div class="busyetmin">
                            -
                        </div>
                    </div>
                </div>
                <div class="BusyBoxControl">
                    <div class="BusyBoxControlHeading">
                        Z OFFSET                      
                    </div>
                    <div class="BusyBoxControlContent" id="busyzo">
                    </div>
                    <div class="BusyBoxControlButton">
                        <div class="busyzoplus">
                            +
                        </div>
                        <div class="busyzomin">
                            -
                        </div>
                    </div>
                </div>
            </div>
            <div>
            <div class="BusyBoxPauseResume">
                    <div class="BusyBoxPauseResumeButton">
                        <div class="busypause">
                            PAUSE
                        </div>
                        <div class="busyresume">
                            RESUME
                        </div>
                        <div class="busycancel">
                            CANCEL
                        </div>
                    </div>
                </div>
            </div>       
        </div>
        <div class="MenuContainer">
            <div class="MenuTabs" id="KRoBoTStatus">
                <?php 
                if($PageState=="control")
                {
                    ?> <a id="KRoBoTHome" style="border: 2px solid; border-color: black;" href="?State=control"></a><?php
                }
                else
                {
                    ?> <a id="KRoBoTHome" href="?State=control"></a><?php
                }
                ?>                    
            </div>
            <div class="MenuTabs">
                
                <?php 
                if($PageState=="terminal")
                {
                    ?> <a style="border: 2px solid; border-color: black;" href="?State=terminal">Terminal</a><?php
                }
                else
                {
                    ?> <a href="?State=terminal">Terminal</a><?php
                }
                ?>
            </div>
            <div class="MenuTabs">
                <?php 
                if($PageState=="offset")
                {
                    ?> <a style="border: 2px solid; border-color: black;" href="?State=offset">Offset/Home</a><?php
                }
                else
                {
                    ?> <a href="?State=offset">Offset/Home</a><?php
                }
                ?>
            </div>
            <div class="MenuTabs">
                <?php 
                if($PageState=="filemanager")
                {
                    ?> <a style="border: 2px solid; border-color: black;" href="?State=filemanager">Files</a><?php
                }
                else
                {
                    ?> <a href="?State=filemanager">Files</a><?php 
                }    
                ?>
            </div>
            <div class="MenuTabs">
                <?php 
                if($PageState=="config")
                {
                    ?> <a style="border: 2px solid; border-color: black;" href="?State=config">Config</a><?php
                }
                else
                {
                    ?> <a href="?State=config">Config</a><?php 
                }
                ?>
            </div>
            <div class="MenuTabs">
                <a href="../KRoBoT.php">MAIN</a>
            </div>
            <div class="MainHeading">KRoBoT
            </div>
        </div>
        <?php
        if(!empty($PageState))
        {
            if($PageState=="control")
            {
                $HomingOverrideData = HomingData($PathToROBOTCFG);
                ?>
                <div class="HideContainer" data-hideid="ContainerInfoC">
                </div>
                <div class="Container" id="ContainerInfoC">                    
                     <div class="ContainerHeading">
                         X LOCATION
                         <div <?php 
                            if(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='xyz')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            else
                            {
                                echo "class='ContainerBox'";
                            }
                            if(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "data-sendcommand='homeall'";
                            } 
                            else
                            {
                                echo "data-sendcommand='homex'";
                            } 
                            ?>>
                            <div class="ContainerBoxOverlayTop" id="rx">
                            </div>
                            <div class="ContainerBoxOverlayBot" id="sx">
                            </div>
                             X
                         </div>                
                     </div>
                     <div class="ContainerHeading">
                         Y LOCATION
                         <div <?php 
                            if(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='xyz')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            else
                            {
                                echo "class='ContainerBox'";
                            }
                            if(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "data-sendcommand='homeall'";
                            } 
                            else
                            {
                                echo "data-sendcommand='homey'";
                            } 
                            ?>>
                             <div class="ContainerBoxOverlayTop" id="ry">
                             </div>
                             <div class="ContainerBoxOverlayBot" id="sy">
                             </div>
                             Y
                         </div>
                     </div>
                     <div class="ContainerHeading">
                         Z LOCATION
                         <div <?php 
                            if(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            elseif(!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='xyz')
                            {
                                echo "class='ContainerBoxHoming'";
                            }
                            else
                            {
                                echo "class='ContainerBox'";
                            }
                            if(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes']))
                            {
                                echo "data-sendcommand='homeall'";
                            } 
                            else
                            {
                                echo "data-sendcommand='homez'";
                            } 
                            ?>>
                             <div class="ContainerBoxOverlayTop" id="rz">
                             </div>
                             <div class="ContainerBoxOverlayBot" id="sz">
                             </div>
                             Z
                         </div>
                     </div>
                     <div class="ContainerHeading">
                         FEED/TEMP
                         <div class="ContainerBoxNoClick">
                             <div class="ContainerBoxOverlaySmallTop" id="t">
                             </div>
                             <div class="ContainerBoxOverlaySmallMid" id="b">
                             </div>
                             <div class="ContainerBoxOverlaySmallBot" id="st">
                             </div>
                             <div class="ContainerBoxOverlay" id="sf">
                             </div>                            
                             F
                         </div>
                     </div>
                     <div class="ContainerHeading">
                         ENDSTOPS
                         <div class="ContainerBox" data-sendcommand="getendstop">
                             <div class="EndStopOverlay" id="endstops">

                             </div>
                             E
                         </div>

                     </div>
                     <?php
         //            echo "<pre>";
         //            print_r($receivecordline);
         //            echo "</pre>";
                     ?>
                 </div>
                <div class="HideContainer" data-hideid="ContainerControls">
                </div>
                <div class="ContainerControls" id="ContainerControls">
                    <div class="ControlMovementContainer" width="400" height="270">
                        <svg class="ControlMovementOverlay" id="SendCordSvg" width="400" height="270" xmlns="http://www.w3.org/2000/svg">
                        </svg>
                        <svg class="ControlMovementOverlay" id="ToolDisplaySvg" width="400" height="270" xmlns="http://www.w3.org/2000/svg">
                        </svg>
                        <div class=ControlZoomSvg>
                            Zoom<br>
                            <button class="ContolZoomInSvg" value="2">+</button>
                            <button class="ContolZoomOutSvg" value="2">-</button>
                            <div class="SvgZoomValue">
                            </div>
                        </div>
                        <div class=ControlOffsetSvg>
                            Offset<br>
                            <button class="ContolOffsetInSvg" value="5">+</button>
                            <button class="ContolOffsetOutSvg" value="5">-</button>
                            <div class="SvgOffsetValue">
                            </div>
                        </div>
                    </div>

                    <div class="MoveSliderContainer">
                        <div id="myControlMoveSpeed" class="ControlMoveSpeedSlider">
                            <input id="MoveSpeedRange" type="range" orient="vertical"  min="60" max="6000" value="500">
                        </div>
                        <div id="myMoveSpeed" class="ControlMoveSpeed">
                            1000
                        </div>
                    </div>
                    <div class="Controlbox">
                        <div class="ControlboxTop">
                            <div class="ControlZDown" id="zdown">
                                Z-
                            </div>
                            <div class="ControlUp" id="yup">
                                Y+
                            </div>
                            <div class="ControlZUp" id="zup">
                                Z+
                            </div>
                        </div>
                        <div class="ControlboxBot">
                            <div class="ControlLeft" id="xdown">
                                X-
                            </div>
                            <div class="ControlDown" id="ydown">
                                Y-
                            </div>
                            <div class="ControlRight" id="xup">
                                X+
                            </div> 
                        </div>
                        <div id="myControlMoveAmount" class="ControlMoveAmountButtons">
                            <button class="ControlButtonAmount active" value="0.01">0.01</button>
                            <button class="ControlButtonAmount" value="0.1">0.1</button>
                            <button class="ControlButtonAmount" value="1">1</button>
                            <button class="ControlButtonAmount" value="5">5</button>
                            <button class="ControlButtonAmount" value="10">10</button>
                            <button class="ControlButtonAmount" value="50">50</button>
                            <input class="ControlButtonAmount" type="text" style="width:50px;">
                            &nbsp;
                            <button class="ControlEUp" id="eup">E+</button>
                            <button class="ControlEDown" id="edown">E-</button>
                        </div>
                    </div>
                </div>
                <div class="HideContainer" data-hideid="ContainerBottom">
                    
                </div>
                <div class="ContainerBottom" id="ContainerBottom">
                    <div class="TerminalOutputMini" id="terminalData">
                    </div>
                    <div class=IframeDIV>
                        <iframe class="tinyfileiframe" src="tinyfile/tinyfilemanager.php?p=">
                        </iframe>
                    </div>
                </div>
                <?php
            }
            if($PageState=="terminal")
            {
                //print_r($_SERVER);
                ?>
                <div class="TerminalOutput" id="terminalData"> 
                </div>
                <div class="TerminalType">
                    <form id="terminaltextform">
                        <input class="terminaltextype" type="text" name="terminaltext" id="terminaltext">
                    </form>                    
                </div>
                <div class="TerminalExtra">
                    <a target="_blank" href="http://<?php echo $_SERVER['SERVER_ADDR'];?>/KRoBoT/GUI/JsonDataOutput.php?data=data&raw=display">RAW</a>
                </div>
<!--                <div id="terminalpushTerminal" style="height: 0px; width: 0px;">
                </div>
                <div id="PushCommandTerminal" style="height: 0px; width: 0px;">
                </div> -->
                <?php
            }
            if($PageState=="offset")
            {
                $ConfigFileBedMeshSettings = RequestConfigGroupData('bed_mesh', $PathToROBOTCFG);
                $ConfigFileStepperXSettings = RequestConfigGroupData('stepper_x', $PathToROBOTCFG);
                $ConfigFileStepperYSettings = RequestConfigGroupData('stepper_y', $PathToROBOTCFG);
                $ConfigFileProbeSettings = RequestConfigGroupData('probe', $PathToROBOTCFG);
                // echo "<pre>";
                // print_r($ConfigFileProbeSettings);
                // echo "</pre>";
                $ConfigFileBLTouchSettings = RequestConfigGroupData('bltouch', $PathToROBOTCFG);                
                ?>
                <div class="Container">
                    <div class="ContainerHeading">
                        MESH ON/OFF
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlay">
                                <?php
                                if($ConfigFileBedMeshSettings[0]=="[bed_mesh]\n")
                                {
                                    echo "ON";
                                }
                                else
                                {
                                    echo "OFF";
                                }
                                ?>
                            </div>
                            M
                        </div>
                    </div>

                    <div class="ContainerHeading">
                        MIN X OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlay">
                                <?php
                                $BedMeshMinPoint = RequestConfigGroupSingleData($ConfigFileBedMeshSettings, 'mesh_min');
                                $BedMeshMinPoint[1] = str_replace(" ", "", $BedMeshMinPoint[1]);
                                $MeshMINXYconfigSplit = explode(",", $BedMeshMinPoint[1]);
                                echo $MeshMINXYconfigSplit[0];
                                ?>
                            </div>
                            X
                        </div>                        
                    </div>
                    <div class="ContainerHeading">
                        MIN Y OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlay">
                                <?php
                                if(!empty($MeshMINXYconfigSplit))
                                {
                                    echo $MeshMINXYconfigSplit[1];
                                }
                                else
                                {
                                    echo "0";
                                }
                                ?>
                            </div>
                            Y
                        </div>                        
                    </div>

                    <div class="ContainerHeading" style="color: red;">
                        MAX X OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlay">
                                <?php
                                $BedMeshMaxPoint = RequestConfigGroupSingleData($ConfigFileBedMeshSettings, 'mesh_max');
                                $BedMeshMaxPoint[1] = str_replace(" ", "", $BedMeshMaxPoint[1]);
                                $MeshMAXXYconfigSplit = explode(",", $BedMeshMaxPoint[1]);
                                echo $MeshMAXXYconfigSplit[0];
                                $BedMeshProbeCount = RequestConfigGroupSingleData($ConfigFileBedMeshSettings, 'probe_count');
                                $BedMeshProbeCount[1] = str_replace(" ", "", $BedMeshProbeCount[1]);
                                $ProbeCountconfigSplit = explode(",", $BedMeshProbeCount[1]);
                                $BedMeshSpeed = RequestConfigGroupSingleData($ConfigFileBedMeshSettings, 'speed');
                                $MeshBedLevelingSpeed = $BedMeshSpeed[1];
                                $ProbeSpeed = RequestConfigGroupSingleData($ConfigFileProbeSettings, 'speed');
                                $ProbingSpeed = $ProbeSpeed[1];
                                $BLTouchSpeed = RequestConfigGroupSingleData($ConfigFileBLTouchSettings, 'speed');
                                //print_r($BLTouchSpeed);
                                if($ProbeSpeed=="OFF")
                                {
                                    $ProbingSpeed = $BLTouchSpeed[1];
                                    if($BLTouchSpeed!="OFF")
                                    {
                                       $BLTouch = 1; 
                                    }  
                                }
                                ?>
                            </div>
                            X
                        </div>                        
                    </div>
                    <div class="ContainerHeading" style="color: red;">
                        MAX Y OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlay">
                                <?php
                                if(!empty($MeshMAXXYconfigSplit))
                                {
                                    echo $MeshMAXXYconfigSplit[1];
                                }
                                else
                                {
                                    echo "0";
                                }
                                ?>
                            </div>
                            Y
                        </div>                        
                    </div>                    
                </div>
                <div class="OffsetMeshContainerForm">
                    <div>                    
                        <form name="Mesh" method="post" action="KRoBoTGUI.php?State=offset">
                            Min X Offset = <input type="number" name="meshminxoffset" min="0" max="1000" value="<?php echo $MeshMINXYconfigSplit[0];?>" step="0.01">
                            Min Y Offset = <input type="number" name="meshminyoffset" min="0" max="1000" value="<?php echo $MeshMINXYconfigSplit[1];?>" step="0.01">
                            <br>
                            Max X Offset = <input type="number" name="meshmaxxoffset" min="0" max="1000" value="<?php echo $MeshMAXXYconfigSplit[0];?>" step="0.01">
                            Max Y Offset = <input type="number" name="meshmaxyoffset" min="0" max="1000" value="<?php echo $MeshMAXXYconfigSplit[1];?>" step="0.01">
                            <br>
                            Probe X Count = <input type="number" name="meshprobexcount" min="3" max="40" value="<?php echo $ProbeCountconfigSplit[0];?>">
                            Probe Y Count = <input type="number" name="meshprobeycount" min="3" max="40" value="<?php echo $ProbeCountconfigSplit[1];?>">
                            <br>
                            Mesh Speed = <input type="number" name="meshlevelingspeed" min="0" max="1000" value="<?php echo $MeshBedLevelingSpeed;?>">
                            Probe Speed = <input type="number" name="probespeed" min="0" max="1000" value="<?php echo $ProbingSpeed;?>">
                            <input type="hidden" name="BLTouch" value="<?php echo $BLTouch;?>">
                            <br>
                            <input type="submit" value="UPDATE">
                        </form>
                    </div>
                </div>
                
                <div class="OffsetMeshContainer">
                    <div class="ContainerHeading">
                        X E OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlayMultiLine">
                                <?php
                                $XPosEndstop = RequestConfigGroupSingleData($ConfigFileStepperXSettings, 'position_endstop');
                                echo "E=".$XPosEndstop[1]."<BR>";
                                $XPosMin = RequestConfigGroupSingleData($ConfigFileStepperXSettings, 'position_min');
                                echo "MIN=".$XPosMin[1]."<BR>";
                                $XPosMax = RequestConfigGroupSingleData($ConfigFileStepperXSettings, 'position_max');
                                echo "MAX=".$XPosMax[1];
                                ?>
                            </div>
                            X
                        </div>                        
                    </div>
                    <div class="ContainerHeading">
                        Y E OFFSET
                        <div class="OffsetMeshContainerBox">
                            <div class="OffsetMeshContainerBoxOverlayMultiLine">
                                <?php
                                $YPosEndstop = RequestConfigGroupSingleData($ConfigFileStepperYSettings, 'position_endstop');
                                echo "E=".$YPosEndstop[1]."<BR>";
                                $YPosMin = RequestConfigGroupSingleData($ConfigFileStepperYSettings, 'position_min');
                                echo "MIN=".$YPosMin[1]."<BR>";
                                $YPosMax = RequestConfigGroupSingleData($ConfigFileStepperYSettings, 'position_max');
                                echo "MAX=".$YPosMax[1];
                                $ZPosEndstop = RequestConfigGroupSingleData($ConfigFileProbeSettings, 'z_offset');
                                //print_r($ZPosEndstop);
                                ?>
                            </div>
                            Y
                        </div>                        
                    </div>
                </div>
                <div class="OffsetMeshContainerForm">
                    <?php
                    $XYZDataUrl = $KRoBoTRootPath."/GUI/XYZData.php";
                    $XYZDataJson = json_decode(file_get_contents($XYZDataUrl), true);                
                    ?>
                    <h3>
                        PART OFFSET
                    </h3>
                    Current Location is equal to = <a title="Click to replace old offset values" href="?State=offset&CXLocation=<?php echo $XYZDataJson['UPDATEVALUES']['RX'];?>&CYLocation=<?php echo $XYZDataJson['UPDATEVALUES']['RY'];?>"><?php echo "X:".$XYZDataJson['UPDATEVALUES']['RX']." AND Y:".$XYZDataJson['UPDATEVALUES']['RY'] ?></a>
                    <div>
                        <form name="Offsets" method="post" action="KRoBoTGUI.php?State=offset">
                            X Endstop = <input type="number" name="xendstopoffset" value="<?php if($ALLGETArgs['CXLocation']=="") echo $XPosEndstop[1]; else echo $ALLGETArgs['CXLocation'];?>" min="-200" max="1200" step="0.01">
                            Y Endstop = <input type="number" name="yendstopoffset" value="<?php if($ALLGETArgs['CYLocation']=="") echo $YPosEndstop[1]; else echo $ALLGETArgs['CYLocation'];?>" min="-200" max="1200" step="0.01">
                            Z Offset = <input type="number" name="zendstopoffset" value="<?php echo $ZPosEndstop[1]?>" min="-100" max="200" step="0.01">
                            <br>
                            X MIN POS= <input type="number" name="xposmin" value="<?php echo $XPosMin[1];?>" min="-200" max="1200" step="0.01">
                            Y MIN POS= <input type="number" name="yposmin" value="<?php echo $YPosMin[1];?>" min="-200" max="1200" step="0.01">
                            <br>
                            X MAX POS = <input type="number" name="xposmax" value="<?php echo $XPosMax[1];?>" min="-200" max="1200" step="0.01">
                            Y MAX POS = <input type="number" name="yposmax" value="<?php echo $YPosMax[1];?>" min="-200" max="1200" step="0.01">
                            <br>
                            <input type="submit" value="UPDATE">
                        </form>
                    </div>
                </div>
                <div class="OffsetMeshContainerForm">
                    <div>
                        <?php
                        $HomingOverrideData = HomingData($PathToROBOTCFG);
//                        echo "<pre>";
//                        print_r($HomingOverrideData);
//                        echo "</pre>";
                        ?>
                        <h3>
                            Homing Override
                        </h3>
                        <form name="Offsets" method="post" action="KRoBoTGUI.php?State=offset">
                            XY<input title="Home XY To ENDSTOP if Z unticked set Z to currect location" type="checkbox" name="homexy" <?php if((empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z') OR  (!empty($HomingOverrideData['gcode']) AND $HomingOverrideData['axes']=='z')) { echo "checked"; }?>>
                            Z Enable<input title="Home Z To ENDSTOP if XY unticked set XY to currect location" type="checkbox" name="homez" <?php if(!empty($HomingOverrideData['gcode'])){ echo "checked"; }?>>
                            G28 ALL<input title="NORMAL HOMING FOR XYZ" type="checkbox" name="homexyz" <?php if(empty($HomingOverrideData['gcode']) AND empty($HomingOverrideData['axes'])){ echo "checked"; }?>>
                            <input type="hidden" name="nohome" value="nohome">
                            <br>
                            <input type="submit" value="UPDATE">
                        </form>
                    </div>
                </div>
                <?php
            }
            if($PageState=="filemanager")
            {
                ?>
                    <div class="ConfigContainer">
                        <a href="tinyfilemanager/tinyfilemanager.php">TinyFileManager</a>
                    </div>
                <?php

            }
            if($PageState=="config")
            {                
                ?>
                <div class="ConfigContainer">
                    <a href="KRoBoTGUI.php?State=config&emptyterminal=1">EMPTY TERMINAL</a><br>
                    <?php
                        if(empty($emptysaveconfig))
                        {
                            ?>
                            <a href="KRoBoTGUI.php?State=config&emptysaveconfig=1">REMOVE SAVE CONFIG</a><br>
                            <?php
                        }
                        else
                        {
                            ?>
                            <a href="KRoBoTGUI.php?State=config&emptysaveconfigverify=1"><font color="red">VERIFY REMOVE SAVE CONFIG</font></a><br>
                            <?php                            
                        }
                    ?>
                    <a href="KRoBoTGUI.php?State=config&ConfigClean=1">CLEAN</a><br>
                    <div class="ConfigExampleLinksWrapper">
                        <a target="_blank" href="../cfg/example.cfg">example.cfg</a>
                        <a target="_blank" href="../cfg/example-extras.cfg">example-extras.cfg</a>
                        <a target="_blank" href="../cfg/example-delta.cfg">example-delta.cfg</a>
                    </div><br>
                    <a href="KRoBoTGUI.php?State=config&BackupConfig=1">BACKUP CONFIG</a>
                        <?php
                            if(!empty($ALLGETArgs['BackupConfig']))
                            {
                                $GetDate = getDate();
                                //print_r($KRoBoT->KRoBoT);
                                $CurrentDateTime = $GetDate['year'].$GetDate['mon'].$GetDate['wday']."_".$GetDate['hours'].":".$GetDate['minutes'].":".$GetDate['seconds'];
                                $ConfigFileName = $KRoBoT->KRoBoT.".cfg";
                                $BackupFileName = $KRoBoT->KRoBoT."-".$CurrentDateTime.".cfg";
                                //echo $BackupFileName;
                                copy("cfg/$ConfigFileName", "cfg/$BackupFileName");
                                usleep(100000);
                                if(file_exists("cfg/$BackupFileName"))
                                {
                                    echo "Backup File $BackupFileName was Created in cfg folder";
                                }
                                else
                                {
                                    echo "Backup File was not created";
                                }
                                //echo $CurrentDateTime;
                                
                            }                        
                        ?>
                    <br>
                    <a href="KRoBoTGUI.php?State=config&EditConfig=1">EDIT CONFIG</a>
                    <div class="CFGSelect">
                        <?php
                        if(!empty($ALLGETArgs['EditConfig']))
                        {
                            ?>
                            EDIT CONFIG
                            <form name="CFGEditing" method="post" action='KRoBoTGUI.php?State=config'>
                                <textarea name="CFGEDITED"><?php
                                foreach($ConfigFileData as $CFGKey=>$CFGValue)
                                {
                                    foreach($CFGValue as $CFGData)
                                    {
                                    echo $CFGData;
                                    }
                                }
                                ?></textarea>
                                <br>
                                <input type="submit">
                            </form>
                            <?php
                            exit;
                        }
                        if(!empty($AllPOSTArgs['CFGEDITED']))
                        {
                            echo "CONFIG FILE EDITTED<br>RESTARTING KLIPPER<br><br>";                            
                            ?>
                            <script> setTimeout(function() { window.location = "KRoBoTGUI.php?State=config"; }, 1000); </script>
                            <?php
                            exit;                            
                            //echo $AllPOSTArgs["CFGEDIT"];
                        }
                        if(empty($AllPOSTArgs['CFGHeading']))
                        {
                            ?>
                            SELECT CFG HEADING TO EDIT:
                            <form name="CFGEditing" method="post" action='KRoBoTGUI.php?State=config'>
                                <select name="CFGHeading">
                                    <?php
                                    foreach($ConfigFileData as $CFGKey=>$CFGValue)
                                    {
                                        ?>
                                        <option>
                                            <?php echo $CFGKey;?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <input type="submit">
                            </form>
                            <?php
                        }
                        else
                        {
                            $ConfigFileEDITGroup = RequestConfigGroupData($AllPOSTArgs['CFGHeading'], $PathToROBOTCFG);
                            if(empty($AllPOSTArgs['CFGOption']))
                            {                                
                                ?>
                                SELECT WHAT TO EDIT:
                                <form name="CFGEditing" method="post" action='KRoBoTGUI.php?State=config'>
                                    <select name="CFGOption">
                                        <?php
                                        foreach($ConfigFileEDITGroup as $CFGKey=>$CFGValue)
                                        {
                                            ?>
                                            <option>
                                                <?php echo $CFGValue;?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="CFGHeading" value="<?php echo $AllPOSTArgs['CFGHeading'];?>">
                                    <input type="submit">
                                </form>
                                <?php
                            }
                            elseif($AllPOSTArgs['CFGOptionEdit']!=1)
                            {
                                $EditConfigOption = RequestConfigGroupSingleData($ConfigFileEDITGroup, $AllPOSTArgs['CFGOption']);
                                ?>
                                CHANGE VALUE AND SUBMIT:
                                <form name="CFGEditing" method="post" action='KRoBoTGUI.php?State=config'>
                                    <input type="text" name="CFGOptionValue" value="<?php echo $EditConfigOption[1];?>"><br>
                                    <input type="hidden" name="CFGHeading" value="<?php echo $AllPOSTArgs['CFGHeading'];?>">
                                    <input type="hidden" name="CFGOption" value="<?php echo $AllPOSTArgs['CFGOption'];?>">
                                    <input type="hidden" name="CFGOptionEdit" value="1">
                                    <input type="submit">
                                </form>
                                <?php                                
                            }
                            else
                            {
                                echo "VALUE UPDATED FOR ".$AllPOSTArgs['CFGHeading']." OPTION ".$AllPOSTArgs['CFGOption']." TO = ".$AllPOSTArgs['CFGOptionValue'];
                                ?>
                                <script> setTimeout(function() { window.location = "KRoBoTGUI.php?State=config"; }, 1000); </script>
                                <?php
                                exit;                                
                            }
                        }
                        ?>
                    </div>
                    <br>
                    <?php
                    
                    echo "<pre>";
                    print_r($ConfigFileData);
                    echo "</pre>";
                    if(!empty($ALLGETArgs['ConfigClean']))
                    {
                        $ConfigFileString;
                        foreach($ConfigFileData as $CFGKey=>$CFGValue)
                        {
                            foreach($CFGValue as $CFGData)
                            {
                            $ConfigFileString .= $CFGData."\n";
                            }
                        }
                        ?>
                        <form name="CFGEditing" method="post" action='KRoBoTGUI.php?State=config'>
                            <input type="hidden" name="CFGEDITED" value="<?php echo $ConfigFileString;?>">
                            <input type="submit" value="CLEAN CONFIG">
                        </form>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
    <div class="HideContainer" data-hideid="BOTTOMCommands">
    </div>
    <div class="BOTTOMCommands">
        <div class="CommandContainer">
            <div class="CCommand" data-commands="ACCEPT" data-command-type="normal" data-coms-type="serial">
                ACCEPT
            </div>
            <div class="CCommand" data-commands="M18" data-command-type="normal" data-coms-type="uds">
                MOTOR OFF
            </div>
            <div class="CCommand" data-commands="G1X0Y0F1000" data-command-type="normal" data-coms-type="serial">
                G1X0Y0
            </div>
            <div class="CCommand" data-commands="RESTART" data-command-type="normal" data-coms-type="serial">
                RESTART
            </div>
            <div class="CCommand" data-commands="FIRMWARE_RESTART" data-command-type="normal" data-coms-type="serial">
                RESTART FIRM
            </div>
        </div>
        <div class="CommandContainer">
            <div class="CCommand" data-ccommand="DELTA_CALIBRATE METHOD=manual">
                DELTA_CALI
            </div>
            <div class="CCommand" data-ccommand="BED_MESH_CALIBRATE">
                BED MESH CAL
            </div>
            <div class="CCommand" data-ccommand="BED_MESH_CLEAR">
                MESH CLEAR
            </div>
            <div class="CCommand" data-ccommand="BED_MESH_MAP">
                MESH MAP
            </div>
        </div>
        <div class="CommandContainer">
            <div class="CCommand" data-ccommand="PREHEAT">
                PREHEAT
            </div>
            <div class="ControlWidget">
                <input id="HeatSelected" type="number" value="50" max="250">
                <button id="HeatBed">
                    BED
                </button>
                <button id="HeatNozzle">
                    NOZZLE
                </button>          
            </div>
            <div class="CCommand" data-ccommand="DISABLEHEAT">
                DISABLEHEAT
            </div>
            <div class="CCommand" data-ccommand="SAVE_CONFIG">
                SAVE CONFIG
            </div>
        </div>
        <div class="CommandContainer">
            <div class="ControlWidgetSpeed">
                <div id="fs">
                    0%
                </div>
                <div>
                    <input id="FS" type="number" value="0" max="100" step="10">
                    <button id="FanSpeedSubmit">
                        FSPEED
                    </button>                      
                </div>       
            </div>
            <div class="ControlWidgetSpeed">
                <div id="psf">
                    100%
                </div>
                <div>
                    <input id="PSF" type="number" value="100" max="300">
                    <button id="PrintSpeedSubmit">
                        PSPEED
                    </button>
                </div>
            </div>
            <div class="ControlWidgetSpeed">
                <div id="esf">
                    100%
                </div>
                <div>
                    <input id="ESF" type="number" value="100" max="300">
                    <button id="ExtrudeSpeedSubmit">
                        EFACTOR
                    </button>
                </div>
            </div>
            <div class="CCommand" data-ccommand="BED_MESH_CALIBRATE  METHOD=manual">
                BED MESH MANUAL
            </div>
        </div>
        <div class="StateCommandContainer">
<!--            <div class="StateCommand" data-ccommand="M112">
                ESTOP
            </div>
            <div class="StateCommand" data-ccommand="PAUSE">
                PAUSE
            </div>
            <div class="StateCommand" data-ccommand="RESUME">
                RESUME
            </div>-->
            <div class="StateCommand" data-ccommand="CANCEL">
                CANCEL
            </div>
        </div>
    </div>
    <?php
    if(!empty($ErrorNotification))
    {
        ?>
        <div class="ErrorNotification">

        </div>        
        <?php           
    }
    ?>
    <div style="height: 100px; width: 100px;">
        <a href="KRoBoTWebcam.php" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST'];?>:8080/?action=stream"/></a>
    </div>
    </body>
</html>