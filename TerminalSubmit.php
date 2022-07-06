<?php
date_default_timezone_set('Africa/Johannesburg');
include 'resources/functions.php';
include '/var/www/html/KRoBoT/resources/PHPClassKRoBoT.php';
//CONNECT TO ACTIVE ROBOT
$PathToROBOT = "/var/www/html/KRoBoT/";
$KRoBoT = ConnectToKRoBoT(0);
chdir("/var/www/html/KRoBoT");
$ALLGETArgs = $_GET;
$AllPOSTArgs = $_POST;
$USleepTime = 2000;
if(!empty($ALLGETArgs['PrinterState']))
{
    if($KRoBoT->Status!="Ready")
    {
        if($ALLGETArgs['PrinterState']=="CANCEL")
        {
            // usleep(50000);
            $KRoBoT->GCodeLoadingKill();
            $KRoBoT->pyGCodeLoaderKill();
            $ClearAndMoveUPCommand = "SAVE_GCODE_STATE NAME=CLEAR_PAUSE<>G91<>G1Z10F1000<>RESTORE_GCODE_STATE NAME=CLEAR_PAUSE<>CLEAR_PAUSE";
            $ClearAndMoveUPCommand = urlencode($ClearAndMoveUPCommand);
            $ClearAndMoveUP = $KRoBoT->Url."&Feeding=".$ClearAndMoveUPCommand;
            json_decode(file_get_contents($ClearAndMoveUP), true);
            $KRoBoT->pyGCodeLoaderReceive("log", "enable", "0.1");
            sleep(1);
            $KRoBoT->SetState("Ready");
        }
        else
        {
            $SendCommandValue = str_replace(" ", "%20", $ALLGETArgs['PrinterState']);
            $ROBOTPushURL = $KRoBoT->Url."&Feeding=".$SendCommandValue;
            $SendCommand = json_decode(file_get_contents($ROBOTPushURL), true);
            if($ALLGETArgs['PrinterState']=="M112")
            {
                $KRoBoT->GCodeLoadingKill();
                $KRoBoT->pyGCodeLoaderKill();
                $KRoBoT->pyGCodeLoaderReceive("log", "enable", "0.1");                
                $KRoBoT->SetState("Ready");
                sleep(1);
                $KRoBoT->Push("Single", "FIRMWARE_RESTART", 0);
            }         
        }
    }
    else
    {
        if($ALLGETArgs['PrinterState']=="CANCEL")
        {
            $KRoBoT->GCodeLoadingKill();
            $KRoBoT->pyGCodeLoaderKill();
            $ClearAndMoveUP = "SAVE_GCODE_STATE NAME=CLEAR_PAUSE<>G91<>G1Z10F1000<>RESTORE_GCODE_STATE NAME=CLEAR_PAUSE";
            //$KRoBoT->SetState("Busy");
            usleep(10000);
            $KRoBoT->Push("Single", $ClearAndMoveUP, 0);
            //$KRoBoT->SetState("Ready");
            $KRoBoT->pyGCodeLoaderReceive("log", "enable", "0.1"); 
        }
        else
        {
            //$KRoBoT->SetState("Busy");
            usleep(10000);
            $KRoBoT->Push("Single", $ALLGETArgs['PrinterState'], 0);
            //$KRoBoT->SetState("Ready");     
        }
    }

}
if($KRoBoT->Status=="Ready")
{
    if(!empty($ALLGETArgs['MoveAxis']) AND !empty($ALLGETArgs['MoveAmount']) AND !empty($ALLGETArgs['MoveSpeed']))
    {
        $MoveCommand = 'SAVE_GCODE_STATE NAME=MoveAxis<>G91<>G1'.$ALLGETArgs['MoveAxis'].$ALLGETArgs['MoveAmount']."F".$ALLGETArgs['MoveSpeed'].'<>RESTORE_GCODE_STATE NAME=MoveAxis<>M114';
        if(!empty($MoveCommand))
        {
            //$KRoBoT->RestartPushControl("commands");
            //$KRoBoT->SetState("Busy");
            usleep(10000);
            $KRoBoT->Push("Single", $MoveCommand, 0);
            //$KRoBoT->SetState("Ready");        
        }   
    }     
    if(!empty($ALLGETArgs['command']))
    {
        if($ALLGETArgs['command']=="homeall")
        {
            $HomeCommand = "G28<>M114";
        }
        if($ALLGETArgs['command']=="homex")
        {
            $HomeCommand = "G28X0<>M114";
        }
        if($ALLGETArgs['command']=="homey")
        {
            $HomeCommand = "G28Y0<>M114";
        }
        if($ALLGETArgs['command']=="homez")
        {
            $HomeCommand = "G28Z0<>M114";
        }
        if($ALLGETArgs['command']=="getendstop")
        {
            $HomeCommand = "M119";
        }
        if(!empty($HomeCommand))
        {
            //$KRoBoT->SetState("Busy");
            usleep(10000);
            $KRoBoT->Push("Single", $HomeCommand, 0);
            //$KRoBoT->SetState("Ready");
        }
    }

    //POST Json REST request
    if(!empty($AllPOSTArgs['terminaltext']) OR !empty($ALLGETArgs['terminaltext']))
    {
        $TCommand = $ALLGETArgs['terminaltext'];
        if(empty($TCommand))
        {
            $TCommand = $AllPOSTArgs['terminaltext'];
        }
        if($TCommand=="PREHEAT" OR $TCommand=="DISABLEHEAT")
        {
            if($TCommand=="PREHEAT")
            {
                $TBuildCommand = "M140 S55<>M104 S180 T0";
            }
            if($TCommand=="DISABLEHEAT")
            {
                $TBuildCommand = "M104 S0<>M140 S0";
            }
            if(!empty($TBuildCommand))
            {
                //$KRoBoT->SetState("Busy");
                usleep(10000);            
                $KRoBoT->Push("Single", $TBuildCommand, 0);
                //$KRoBoT->SetState("Ready");
            }
        }
        else
        {
            if(!empty($AllPOSTArgs['terminaltext']))
            {
                $terminaltext = $AllPOSTArgs['terminaltext'];
            }
            if(!empty($ALLGETArgs['terminaltext']))
            {
                $terminaltext = $ALLGETArgs['terminaltext'];
            }
            $KRoBoT->Push("Single", $terminaltext, 0);
        }
    }
}
