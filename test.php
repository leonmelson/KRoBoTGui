<!DOCTYPE html>
<html>
<body>

<canvas id="myCanvas" width="400" height="270" style="border:1px solid #d3d3d3;">
</canvas>

<script>

var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
//Zero Location
ctx.lineCap = 'round';
ctx.strokeStyle = 'green';
ctx.beginPath();
ctx.moveTo(0, 0);
ctx.lineTo(0, 0);
ctx.lineWidth = 30;

//Sent Movements
ctx.stroke();
ctx.lineCap = 'round';
ctx.strokeStyle = 'blue';
ctx.beginPath();
ctx.moveTo(1.25, 1.25);
ctx.lineTo(1.25, 268.750);
ctx.lineWidth = 5;
ctx.stroke();
ctx.beginPath();
ctx.moveTo(1.25, 268.750);
ctx.lineTo(398.750, 268.750);
ctx.lineWidth = 5;
ctx.stroke();

//Tool location
ctx.lineCap = 'round';
ctx.strokeStyle = 'red';
ctx.beginPath();
ctx.moveTo(398.750, 268.750);
ctx.lineTo(398.750, 268.750);
ctx.lineWidth = 20;
ctx.stroke();
</script>

</body>
</html>