<?php
date_default_timezone_set('Africa/Johannesburg');
header("Content-Type: application");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('resources/MainFunctions.php');

$GetJsonTerminalData = json_decode(file_get_contents('tmp/terminal.json'), true);
foreach($GetJsonTerminalData['terminal']['receivecord'] as $receivecordline)
{
    $XYZCords = explode(" ", $receivecordline['Data']);
    foreach($XYZCords as $Cords)
    {
        $Cord = explode(":", $Cords);
        if($Cord[0]=="X")
        {
            $RX = $Cord[1];
        }
        if($Cord[0]=="Y")
        {
            $RY = $Cord[1];
        }
        if($Cord[0]=="Z")
        {
            $RZ = $Cord[1];
        }
    }
}
foreach($GetJsonTerminalData['terminal']['sendcord'] as $sendcordline)
{
    $SXYZCords = explode(" ", $sendcordline['Data']);
    foreach($SXYZCords as $SCords)
    {
        if($SCords[0]=="X")
        {
            $SCords = str_replace("X", "", $SCords);
            $SX = $SCords;
        }
        if($SCords[0]=="Y")
        {
            $SCords = str_replace("Y", "", $SCords);
            $SY = $SCords;
        }
        if($SCords[0]=="Z")
        {
            $SCords = str_replace("Z", "", $SCords);
            $SZ = $SCords;
        }
        if($SCords[0]=="F")
        {
            $SCords = str_replace("F", "", $SCords);
            $SF = $SCords;
        }
    }
}
?>
<html>
    <head>
        <title>
            
        </title>
        <link rel="stylesheet" href="resources/css.css">
    </head>
    <body>
        <div class="XYZOutput" id="xyzcords">
            <div class="Cords">
                <div class="CordsBackGround">
                    <div class="RCordsOverlay"><?php if(!empty($RX)){ echo $RX; }?></div>
                    <div class="SCordsOverlay"><?php if(!empty($SX)){ echo $SX; }?></div>
                    X
                </div>
            </div>
            <div class="Cords">
                <div class="CordsBackGround">
                    <div class="RCordsOverlay"><?php if(!empty($RY)){ echo $RY; }?></div>
                    <div class="SCordsOverlay"><?php if(!empty($SY)){ echo $SY; }?></div>
                    Y
                </div>
            </div>
            <div class="Cords">
                <div class="CordsBackGround">
                    <div class="RCordsOverlay"><?php if(!empty($RZ)){ echo $RZ; }?></div>
                    <div class="SCordsOverlay"><?php if(!empty($SZ)){ echo $SZ; }?></div>
                    Z
                </div>
            </div>
            <div class="Cords">
                <div class="CordsBackGround">
                    <div class="FCordsOverlay"><?php if(!empty($SF)){ echo $SF; }?></div>
                    F
                </div>
            </div>
            <?php
//            echo "<pre>";
//            print_r($receivecordline);
//            echo "</pre>";
            ?>
        </div>
<!--        <div  class="RequestScaleData" id="temp">
            Temp
        </div>-->
    </body>
</html>
