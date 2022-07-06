<?php
$pushpagedata[$PushPage]['page'] = "test1";
$pushpagedata[$PushPage]['time'] = "write1";
file_put_contents("/home/pi/pushcontrol.json", json_encode($pushpagedata));
