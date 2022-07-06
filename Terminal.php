<?php
date_default_timezone_set('Africa/Johannesburg');
include('resources/functions.php');
$lines=array();
$logfile = "/home/pi/.octoprint/logs/serial.log";
$logfilesize = filesize($logfile);

//$Truncate = shell_exec('truncate -s 0 /home/pi/.octoprint/logs/serial.log');
//echo $Truncate;
?>

<html>
    <head>
        <title>
        KlipperCNC Terminal DATA
        </title>
        <link rel="stylesheet" href="resources/css.css">
        <style> 
            input[type=text], input[type=submit] {
            background-color: black;
            font-size: 16px;
            font-weight: 200;
            width: 900px;
            color: white;
            text-align: left;
            }
        </style>
        <script type="text/javascript" src="resources/jquery-1.12.3.min.js">
        </script>
        <script type="text/javascript" language="javascript">
            <?php
            include('resources/java.php');
            ?>
        </script>
    <script>
        $(function () {
          $('form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
              type: 'post',
              url: 'TerminalSubmit.php',
              data: $('form').serialize(),
            });
            $("#terminaltext").val("");
          });
        });        
    </script>
    </head>
    <body>
        <p id="terminalpush">
        </p>
        <div class="TerminalHead">
           KlipperCNC Terminal
        </div>
        <div class="TerminalDisplay" id="terminal">
            Terminal
        </div>
        <div class="TerminalType">
        <form>
        <input type="text" name="terminaltext" id="terminaltext">
        </form>
        </div>
    </body>
</html>
