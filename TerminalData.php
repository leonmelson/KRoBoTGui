<?php
$data = $_GET['data'];
if(!empty($data))
{
    date_default_timezone_set('Africa/Johannesburg');
    $CountTerminalDataRows = "";

    $GetJsonTerminalData = json_decode(file_get_contents('tmp/terminal.json'), true);
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
    for($x = 0; $x <= $CountTerminalDataRows-20; $x++)
    {
        array_shift($GetJsonTerminalData['terminal'][$data]);
    }
    foreach($GetJsonTerminalData['terminal'][$data] as $key => $value)
    {
        if (strlen($value['Data']) > 40)
        $GetJsonTerminalData['terminal'][$data][$key]['Data'] = substr($value['Data'], 0, 40);
    }
    if(count($GetJsonTerminalData['terminal'][$data])<=20)
    {
    echo json_encode($GetJsonTerminalData['terminal'][$data]);
    }
}
