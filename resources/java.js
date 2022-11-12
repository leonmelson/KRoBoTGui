var api_key = '9ca5473290852d5320640a5fcb79e068';
var api_url = 'http://192.168.0.52:5432/api';
var robot_name = "";
var robot_api_url = "";

function get_robot_help(){
    var get_robot_help = {"id": "123", "method": "get_help"};
	 $.ajax({
	    type: "POST",
		url:api_url,
		data: JSON.stringify(get_robot_help),
		success:function(robot_help_data){
		    //console.log(robot_help_data)
			$.each( robot_help_data["robots"], function( robot_names, robot_data ) {

				 if(robot_data["active"] && robot_data["primary"]){
					 console.log(robot_names);
					 robot_name = robot_names;
					 robot_api_url = "http://" + robot_data["api_url"];
				 }
			});

			$('document').ready(function(){

				$('#KRoBoTHome').html(robot_name);

				function isDoubleClicked(element) {
					//if already clicked return TRUE to indicate this click is not allowed
					if (element.data("isclicked")) return true;

					//mark as clicked for 1 second
					element.data("isclicked", true);
					setTimeout(function () {
						element.removeData("isclicked");
					}, 1000);

					//return FALSE to indicate this click was allowed
					return false;
				}
				if(document.getElementById("myControlMoveAmount") !== null)
				{
					var Direction = ["up", "down"];
					Direction["up"] = ["ControlUp", "ControlZUp", "ControlRight", "ControlEUp"];
					Direction["down"] = ["ControlZDown", "ControlLeft", "ControlDown", "ControlEDown"];
					var MoveAmount = '0.01';
					var MoveSpeed = '500';
					var MoveSpeedRange = $('#MoveSpeedRange');
					var myMoveSpeed = $('#myMoveSpeed');

					myMoveSpeed.html(MoveSpeedRange.attr('value'));

					MoveSpeedRange.on('input', function(){
						myMoveSpeed.html(this.value);
						MoveSpeed = this.value;
					});

					var MoveAmountDIV = document.getElementById("myControlMoveAmount");
					var MoveBTNS = MoveAmountDIV.getElementsByClassName("ControlButtonAmount");
					for (var i = 0; i < MoveBTNS.length; i++) {
					  if(i < 6)
					  {
						MoveBTNS[i].addEventListener("click", function() {
							var current = document.getElementsByClassName("active");
							current[0].className = current[0].className.replace(" active", "");
							this.className += " active";
							MoveAmount = this.value;
							//alert(i);
						});
					  }
					  else if(i == 6)
					  {
						MoveBTNS[i].addEventListener("change", function() {
						  var current = document.getElementsByClassName("active");
						  current[0].className = current[0].className.replace(" active", "");
						  this.className += " active";
						  MoveAmount = this.value;
						  //alert(i);
					  });
					  }
					}
					$.each( Direction, function( Directionkey, Directionvalue )
					{
						$.each( Direction[Directionvalue], function( DirectionvalueKey, DirectionvalueValue )
						{
							var PlusMinus = "";
							if(Directionvalue == "down")
							{
								PlusMinus = "-";
							}
							var MoveID = $('.'+DirectionvalueValue).attr('id');
							var MoveAxis = MoveID.replace(Directionvalue, "");
							$('#'+MoveID).click(function(){
								// if (isDoubleClicked($(this))) return;
								m_commands = ['JOG ' + MoveAxis.toUpperCase() + '=' + PlusMinus + MoveAmount + ' F=' + MoveSpeed];
                                var m_send_commands_api_list = {"commands_type": "jog", "commands": m_commands, "coms_type": "serial"};
                                var m_send_commands_to_api = {"id": "3000001", "method": "send_gcode_commands", "api_key": api_key, "robot": robot_name, "params": m_send_commands_api_list};
						        console.log(m_send_commands_to_api);
                                $.ajax({
                                    type: "POST",
                                    url: robot_api_url,
                                    data: JSON.stringify(m_send_commands_to_api),
                                    success:function(){
                                    }
                                });
							  });
						});
					});
				}
				if(document.getElementById("KRoBoTStatus") !== null)
				{
					var KRoBoTName = "";
					var KRoBoTState = "Offline";
					var PSFVALUE = 100;
					var FSVALUE = 100;
					var ESFVALUE = 0;
					var BEDTOTEMP = 0.0;
					var TOTEMP = 0.0;
					var ServerIP = location.hostname;
					var SvgZoomPercetageValue;
					var TerminalPreSendCordX = 0;
					var TerminalPreSendCordY = 0;
					var TerminalPreSendCordKey = 0;
					var viewBoxZX = 270;
					var viewBoxZY = 400;
					var viewBoxOffset = 0;
					const ToolDisplaySvg = document.getElementById("ToolDisplaySvg");
					const SendCordSvg = document.getElementById("SendCordSvg");
					//Zoom into XY
					var SvgZoomValue = 100;
					var SvgOffsetValue = 0;

					function GetKRoBoTStatus(){
						var get_robot_status = {"id": "123", "method": "get_robot_status", "api_key": api_key, "robot": robot_name}
						$.ajax({
							type: "POST",
							url:robot_api_url,
							data: JSON.stringify(get_robot_status),
							success:function(GetKRoBoTStatus){
								//console.log(GetKRoBoTjson);

								if(GetKRoBoTStatus['status']=="ready")
								{
									KRoBoTState = GetKRoBoTStatus['status'];
									document.title = 'KRoBoT';
									$('#KRoBoTHome').attr('title', 'HOME');
									$('a').css({color: 'black','text-shadow':'0px 0px black','font-size':'20px','font-weight':'bold'});
									$('.BusyBox').hide();
								}
								else
								{
									KRoBoTState = GetKRoBoTStatus['status']
									$('a').css({color: 'red','text-shadow':'1px 1px yellow','font-size':'20px'});
								}
								if(GetKRoBoTStatus['status']=="busy")
								{
									$('.BusyBox').show();
								}
								;
							},
							complete:function(){
								setTimeout(function(){GetKRoBoTStatus();}, 1000);
							}
						 });

					 }
					 GetKRoBoTStatus();

					$('.SvgZoomValue').html(SvgZoomValue + " %");
					$('.ContolZoomInSvg').click(function(){
						SvgZoomValue = parseFloat(SvgZoomValue) + parseFloat($(this).val());
						$('.SvgZoomValue').html(SvgZoomValue + " %");
						SvgZoomPercetageValue = SvgZoomValue/100;
						SvgZoomPercetageValue = (1 - SvgZoomPercetageValue) + 1;
						//alert(SvgZoomPercetageValue);
						//Zoom XY
						if(SvgZoomValue>0)
						{
							viewBoxZX = Math.round(270 * SvgZoomPercetageValue);
							viewBoxZY = Math.round(400 * SvgZoomPercetageValue);
						}
						SendCordSvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
						ToolDisplaySvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
					});
					$('.ContolZoomOutSvg').click(function(){
						SvgZoomValue = parseFloat(SvgZoomValue) - parseFloat($(this).val());
						$('.SvgZoomValue').html(SvgZoomValue + " %");
						SvgZoomPercetageValue = SvgZoomValue/100;
						SvgZoomPercetageValue = (1 - SvgZoomPercetageValue) + 1;
						//alert(SvgZoomPercetageValue);
						//Zoom XY
						if(SvgZoomValue>0)
						{
							viewBoxZX = Math.round(270 * SvgZoomPercetageValue);
							viewBoxZY = Math.round(400 * SvgZoomPercetageValue);
						}
						SendCordSvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
						ToolDisplaySvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
					});


					$('.SvgOffsetValue').html(SvgOffsetValue + " mm");
					$('.ContolOffsetInSvg').click(function(){
						SvgOffsetValue = parseFloat(SvgOffsetValue) - parseFloat($(this).val());
						$('.SvgOffsetValue').html(SvgOffsetValue + " mm");

						SendCordSvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
						ToolDisplaySvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
					});
					$('.ContolOffsetOutSvg').click(function(){
						SvgOffsetValue = parseFloat(SvgOffsetValue) + parseFloat($(this).val());
						$('.SvgOffsetValue').html(SvgOffsetValue + " mm");

						SendCordSvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
						ToolDisplaySvg.setAttribute("viewBox", SvgOffsetValue + " " + SvgOffsetValue +" " + viewBoxZY + " " + viewBoxZX);
					});
					function TerminalSendCord(){
						 $.ajax({
								url:'TerminalSendCord.php?data=sendcord', success:function(Terminalsendcord){
								//console.log(Terminalsendcord);
								var getterminalsendcordarray = jQuery.parseJSON(Terminalsendcord);

								$.each( getterminalsendcordarray, function( TerminalSendCordKey, TerminalSendCordValue ) {
								  if(parseInt(TerminalPreSendCordKey, 10) < parseInt(TerminalSendCordKey, 10))
								  {
									if(TerminalSendCordValue['G1'] != undefined)
									{
									  if(TerminalSendCordValue['G1']['Y'] === undefined)
									  {
										TerminalSendCordValue['G1']['Y'] = TerminalPreSendCordY;
									  }
									  if(TerminalSendCordValue['G1']['X'] === undefined)
									  {
										TerminalSendCordValue['G1']['X'] = TerminalPreSendCordX;
									  }

									  const drawCords = document.createElementNS("http://www.w3.org/2000/svg", "line");
									  //console.log(' Array Loop = ' + TerminalSendCordKey + ' Pre Key = ' + TerminalPreSendCordKey);
									  //console.log(' x1 = ' + TerminalPreSendCordY + ' x2 = ' + TerminalSendCordValue['Y']);
									  //console.log(' y1 = ' + TerminalPreSendCordX + ' y2 = ' + TerminalSendCordValue['X']);
									  drawCords.setAttribute("x1", TerminalPreSendCordY);
									  drawCords.setAttribute("x2", TerminalSendCordValue['G1']['Y']);
									  drawCords.setAttribute("y1", TerminalPreSendCordX);
									  drawCords.setAttribute("y2", TerminalSendCordValue['G1']['X']);
									  drawCords.setAttribute("stroke", "#F5DEB3");

									  SendCordSvg.appendChild(drawCords);

									  TerminalPreSendCordY = TerminalSendCordValue['G1']['Y'];
									  TerminalPreSendCordX = TerminalSendCordValue['G1']['X'];
									}
									else if(TerminalSendCordValue['G2'] != undefined || TerminalSendCordValue['G3'] != undefined )
									{
									  //TODO IF the PreX or Y is Equal to the End Arc XY use Circle instead Also
									  var ArcDirection;
									  var Radius;
									  var ArcCoords = "M " + TerminalPreSendCordY + " " + TerminalPreSendCordX + " A ";
									  const drawArc = document.createElementNS("http://www.w3.org/2000/svg", "path");
									  if(TerminalSendCordValue['G2'] !== undefined)
									  {
										Radius = Math.sqrt(Math.pow(Math.abs(TerminalSendCordValue['G2']['I']), 2) + Math.pow(Math.abs(TerminalSendCordValue['G2']['J']), 2))
										ArcDirection = 1;
										ArcCoords += Radius + " " + Radius + " 0 1 " + ArcDirection + " " + TerminalSendCordValue['G2']['Y'] + " " +TerminalSendCordValue['G2']['X'];
										drawArc.setAttributeNS(null, 'stroke', "#F5DEB3");
										drawArc.setAttributeNS(null, 'stroke-width', 1);
										drawArc.setAttributeNS(null, 'stroke-linejoin', "round");
										drawArc.setAttributeNS(null, 'd', ArcCoords);
										drawArc.setAttributeNS(null, 'fill', "url(#gradient)");
										drawArc.setAttributeNS(null, 'opacity', 1.0);
										SendCordSvg.appendChild(drawArc);
										//alert(ArcCoords);
										TerminalPreSendCordY = TerminalSendCordValue['G2']['Y'];
										TerminalPreSendCordX = TerminalSendCordValue['G2']['X'];
									  }
									  else if(TerminalSendCordValue['G3'] !== undefined)
									  {
										Radius = Math.sqrt(Math.pow(Math.abs(TerminalSendCordValue['G3']['I']), 2) + Math.pow(Math.abs(TerminalSendCordValue['G3']['J']), 2))
										ArcDirection = 0;
										ArcCoords += Radius + " " + Radius + " 0 0 " + ArcDirection + " " + TerminalSendCordValue['G3']['Y'] + " " +TerminalSendCordValue['G3']['X'];
										//alert(ArcCoords);
										drawArc.setAttributeNS(null, 'stroke', "#F5DEB3");
										drawArc.setAttributeNS(null, 'stroke-width', 1);
										drawArc.setAttributeNS(null, 'stroke-linejoin', "round");
										drawArc.setAttributeNS(null, 'd', ArcCoords);
										drawArc.setAttributeNS(null, 'fill', "url(#gradient)");
										drawArc.setAttributeNS(null, 'opacity', 1.0);
										SendCordSvg.appendChild(drawArc);

										TerminalPreSendCordY = TerminalSendCordValue['G3']['Y'];
										TerminalPreSendCordX = TerminalSendCordValue['G3']['X'];
									  }
									}
									TerminalPreSendCordKey = TerminalSendCordKey;
								  }
								});
							 },
							 complete:function(){
								 //console.log(TerminalPreSendCordKey);
								 setTimeout(function(){TerminalSendCord();}, 500);
							 }
						 });
					 }
					TerminalSendCord();
					function XYZData(){
					    var get_klipper_api_info_params = {"klipper_api_method": "objects/query", "klipper_api_params": {"objects": {"gcode_move": ["gcode_position", "position", "absolute_coordinates", "speed_factor", "extrude_factor", "homing_origin"], "query_endstops": null, "fan": ["speed"], "system_stats": ["sysload", "cputime"]}}};
					    var XYZ_data = {"id": "123", "method": "get_klipper_api_info", "api_key": api_key, "robot": robot_name, "params": get_klipper_api_info_params};
						 $.ajax({
                            type: "POST",
                            url:robot_api_url,
                            data: JSON.stringify(XYZ_data),
							 success:function(data){
							    var getarray = data[0];
								console.log(getarray);
								$("#ToolDisplaySvg").empty();
								var MoveLocation = ['sx', 'sy', 'rx', 'ry'];
								var PreValue = ['FS', 'PSF', 'ESF'];
								var ProbeLocation = getarray['PROBE'];
								PSFVALUE = (getarray['result']['status']['gcode_move']['speed_factor']*100);
								ESFVALUE = (getarray['result']['status']['gcode_move']['extrude_factor']*100);
								FSVALUE = (getarray['result']['status']['fan']['speed']*100);

								PROGRESS = getarray['PROGRESS'];
								if(KRoBoTState=="Busy")
								{
								  if(PROGRESS > 0){
									document.title = 'KRoBoT ' + PROGRESS + '%';
									$('#KRoBoTHome').attr('title', KRoBoTName + " " + PROGRESS + '%');
								  }
								}
								//$('#FS').val(GetXYZData['UPDATEVALUES']['FS']*100);
								//$('#PSF').val(GetXYZData['UPDATEVALUES']['PSF']*100);
								//$('#ESF').val(GetXYZData['UPDATEVALUES']['ESF']*100);
								$.each( getarray['UPDATEVALUES'], function( key, value ) {
									var Keyvalue = key.toLowerCase();
									if(key == 'FS' || key == 'PSF' || key == 'ESF'){
										if(value == ""){
										   $('#' + Keyvalue).text("0%");
										   $('#busy' + Keyvalue).text("0%");
										   $('#' + Keyvalue).val(Math.round((value*100)));
										}
										else{
											if(PreValue[key] != value)
											{
											  $('#' + Keyvalue).html(Math.round((value*100)) + "%");
											  $('#busy' + Keyvalue).html(Math.round((value*100)) + "%");
											}

										}
									}
									else{
										if(value == ""){
										   $('#' + Keyvalue).text("0.000");
										   MoveLocation[Keyvalue] = "0.000";
										}
										else{
											$('#' + Keyvalue).html(value);
											MoveLocation[Keyvalue] = value;

										}
									}
									if(key == 'B'){
										var BEDTEMP = value.split("/");
										BEDTOTEMP = BEDTEMP[1];

										if(BEDTOTEMP == ""){
											$('#busybt').text("0.00");
										 }
										 else{
											 $('#busybt').html(BEDTOTEMP);
										 }
									}
									if(key == 'T'){
										var TEMP = value.split("/");
										TOTEMP = TEMP[1];
										if(TOTEMP == ""){
											$('#busyet').text("0.00");
										 }
										 else{
											 $('#busyet').html(TOTEMP);
										 }
									}
									if(key == 'GCOZ'){
									  if(TOTEMP == ""){
										  $('#busyzo').text("0.00");
									   }
									   else{
										   $('#busyzo').html(value);
									   }
									}
									PreValue[key] = value;
								});
								var ProbeTextColor;
								var ProbeHeightArray = new Array();
								if(typeof ProbeLocation === 'object')
								{
									$.each( getarray['PROBE'], function( ProbeKey, ProbeArray ) {
										//alert(ProbeArray['X']);
										if(ProbeArray['Z'] > 0){
											ProbeTextColor = 'blue';
										}
										else if(ProbeArray['Z'] < 0){
											ProbeTextColor = 'red';
										}
										else{
											ProbeTextColor = 'green';
										}
										if(ProbeArray['Z'] != ""){
											ProbeHeightArray.push(ProbeArray['Z']);
										}

										const ProbePoints = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
										ProbePoints.setAttribute('cx', ProbeArray['X']);
										ProbePoints.setAttribute('cy', ProbeArray['Y']);
										ProbePoints.setAttribute("r", "2");
										ProbePoints.setAttribute("fill", "white");

										const ProbePointsZ = document.createElementNS('http://www.w3.org/2000/svg', 'text');
										ProbePointsZ.setAttribute('x', ProbeArray['X']);
										ProbePointsZ.setAttribute('y', ProbeArray['Y']);
										ProbePointsZ.setAttribute("fill", ProbeTextColor);
										ProbePointsZ.setAttribute("font-size", "10");
										ProbePointsZ.textContent = ProbeArray['Z'];

										ToolDisplaySvg.appendChild(ProbePoints);
										ToolDisplaySvg.appendChild(ProbePointsZ);
									});
									// console.log(ProbeHeightArray);
									if(ProbeHeightArray !== null){
										var MaxMin = parseFloat(Math.max(...ProbeHeightArray)-Math.min(...ProbeHeightArray));
										const ProbeMaxMin = document.createElementNS('http://www.w3.org/2000/svg', 'text');
										ProbeMaxMin.setAttribute('x', '15');
										ProbeMaxMin.setAttribute('y', '15');
										ProbeMaxMin.setAttribute("fill", 'black');
										ProbeMaxMin.setAttribute("font-size", "15");
										ProbeMaxMin.textContent = "Max-Min=" + MaxMin;
										ToolDisplaySvg.appendChild(ProbeMaxMin);
									}
								}
								if(document.getElementById("ToolDisplaySvg") !== null)
								{
									if(SvgOffsetValue>0)
									{
										MoveLocation['rx'] = parseFloat(MoveLocation['rx']);
										MoveLocation['ry'] = parseFloat(MoveLocation['ry']);
										MoveLocation['sx'] = parseFloat(MoveLocation['sx']);
										MoveLocation['sy'] = parseFloat(MoveLocation['sy']);
									}
									if(MoveLocation['rx']  && MoveLocation['ry'])
									{
										// create a circle
										const scir = document.createElementNS("http://www.w3.org/2000/svg", "circle");
										scir.setAttribute("cx", MoveLocation['ry']);
										scir.setAttribute("cy", MoveLocation['rx']);
										scir.setAttribute("r", "2");
										scir.setAttribute("fill", "yellow");

										// attach it to the container
										ToolDisplaySvg.appendChild(scir);

									}
									if(MoveLocation['sx']  && MoveLocation['sy'])
									{
										// create a circle
										const rcir = document.createElementNS("http://www.w3.org/2000/svg", "circle");
										rcir.setAttribute("cx", MoveLocation['sy']);
										rcir.setAttribute("cy", MoveLocation['sx']);
										rcir.setAttribute("r", "2");
										rcir.setAttribute("fill", "blue");

										// attach it to the container
										ToolDisplaySvg.appendChild(rcir);
									}

									var Endstop = '';
									$.each( getarray['ENDSTOP'], function( EndstopKey, EndstopValue ) {
										 Endstop = Endstop + EndstopKey.toUpperCase() + " = " + EndstopValue + "<br>";
										});
									$('#endstops').html(Endstop);

									//Zero Location
									// create a circle
									const zcir = document.createElementNS("http://www.w3.org/2000/svg", "circle");
									zcir.setAttribute("cx", 0);
									zcir.setAttribute("cy", 0);
									zcir.setAttribute("r", "3");
									zcir.setAttribute("fill", "green");

									// attach it to the container
									ToolDisplaySvg.appendChild(zcir);
								}

							 }, complete:function(data){
								 setTimeout(function(){XYZData();}, 250);
								}
						 });
					 }
					XYZData();
				}
				setInterval(PushCommand, 10000);
				function PushCommand() {
				$('#PushCommandControl').load('PushCommand.php?page=control #PushCommandControl', function() {
				});
				$('#PushCommandTerminal').load('PushCommand.php?page=terminal #PushCommandTerminal', function() {
				});
				}
				setInterval(TerminalPush, 500);
				function TerminalPush() {
				$('#terminalpushControl').load('terminaltojson.php?page=control #terminalpushControl', function() {
				});
				$('#terminalpushTerminal').load('terminaltojson.php?page=terminal #terminalpushTerminal', function() {
				});
				}
				$('.HideContainer').click(function(){
					//var HideContainerID = $(this).attr('data-hideid');
					$('.BOTTOMCommands').slideToggle(500);
				});
				if(document.getElementById("terminaltextform") !== null)
				{
					$('#terminaltext').focus();

					$('#terminaltextform').on('submit', function (e) {
						e.preventDefault();
				        var t_commands = $('#terminaltextform').serialize();
						t_commands = decodeURIComponent(t_commands)
						//alert(t_commands)

				        var td_command_slice = t_commands.slice(13);
				        //alert(td_command_slice)

						var t_command_array = td_command_slice.split('/');
                        //alert(t_command_array.toString())
                        if ($.isArray(t_command_array))
                        {
                            t_send_commands = t_command_array;
                        }
                        else
                        {
                            t_send_commands = [t_commands];
                        }
                        var t_send_commands_api_list = {"commands_type": "normal", "commands": t_send_commands, "coms_type": "serial"};
                        var t_send_commands_to_api = {"id": "3000001", "method": "send_gcode_commands", "api_key": api_key, "robot": robot_name, "params": t_send_commands_api_list};
						console.log(t_send_commands_to_api);
                        $.ajax({
                            type: "POST",
                            url: robot_api_url,
                            data: JSON.stringify(t_send_commands_to_api),
                            success:function(){
                            }
                        });
						$("#terminaltext").val("");
					});
				}

				$('.ContainerBox').click(function(){
					if (isDoubleClicked($(this))) return;
					var SendCommand = $(this).attr('data-sendcommand');
					$.ajax({
					  url: 'TerminalSubmit.php?command='+SendCommand,
					  success:function(){
					  }
					});
				  });
				$('.CCommand, #ESTOP, .StateCommand, .ContainerBoxHoming').click(function(){
					if (isDoubleClicked($(this))) return;
					var commands = $(this).attr('data-commands'); // Commands can be split in /
					var command_array = commands.split('/');
					var commands_type = $(this).attr('data-commands-type');
					var coms_type = $(this).attr('data-coms-type');
					var send_commands = "";
					if ($.isArray(command_array))
					{
						send_commands = command_array;
					}
					else
					{
						send_commands = [commands];
					}
					if (commands_type == null)
					{
						commands_type = "normal";
					}
					if (coms_type == null)
					{
						coms_type = "serial";
					}
					var send_commands_api_list = {"commands_type": commands_type, "commands": send_commands, "coms_type": coms_type};
					var send_commands_to_api = {"id": "2000001", "method": "send_gcode_commands", "api_key": api_key, "robot": robot_name, "params": send_commands_api_list};
					console.log(send_commands_to_api);

					$.ajax({
						type: "POST",
					  	url: robot_api_url,
						data: JSON.stringify(send_commands_to_api),
					  	success:function(){
					  	}
					});
				  });    
				  
				var HeatAmount = '50';
				$('#HeatSelected').on('input', function(){
					HeatAmount = this.value;
				});
				$('#HeatBed').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M140 S" + HeatAmount; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busybtplus').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M140 S" + (parseInt(BEDTOTEMP)+1); 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busybtmin').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M140 S" + (parseInt(BEDTOTEMP)-1); 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				$('#HeatNozzle').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M104 S" + HeatAmount + " T0"; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyetplus').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M104 S" + (parseInt(TOTEMP)+1) + " T0"; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyetmin').click(function(){
					if (isDoubleClicked($(this))) return;
					var BedTempCommand = "M104 S" + (parseInt(TOTEMP)-1) + " T0"; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+BedTempCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyzoplus').click(function(){
					if (isDoubleClicked($(this))) return;
					var ZOffsetCommand = "SET_GCODE_OFFSET Z_ADJUST=0.01 MOVE=1"; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+ZOffsetCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyzomin').click(function(){
					if (isDoubleClicked($(this))) return;
					var ZOffsetCommand = "SET_GCODE_OFFSET Z_ADJUST=-0.01 MOVE=1"; 
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+ZOffsetCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busypause').click(function(){
					if (isDoubleClicked($(this))) return;
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState=PAUSE',
					  success:function(){
					  }
					});
				  });
				  $('.busyresume').click(function(){
					if (isDoubleClicked($(this))) return;
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState=RESUME',
					  success:function(){
					  }
					});
				  });
				  $('.busycancel').click(function(){
					if (isDoubleClicked($(this))) return;
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState=CANCEL',
					  success:function(){
					  }
					});
				  });
				$('.StateCommand').click(function(){
					if (isDoubleClicked($(this))) return;
					var SCommand = $(this).attr('data-ccommand');
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.StateCommand').click(function(){
					if (isDoubleClicked($(this))) return;
					var SCommand = $(this).attr('data-ccommand');
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				//   $('.ESTOP').click(function(){
				// 	if (isDoubleClicked($(this))) return;           
				// 	SCommand = "M112";
				// 	$.ajax({
				// 	  url: 'TerminalSubmit.php?PrinterState='+SCommand,
				// 	  success:function(){
				// 	  }
				// 	});
				//   });
				  $('.busypsfplus').click(function(){
					if (isDoubleClicked($(this))) return;           
					SCommand = "M220 S" + (PSFVALUE+1);
					$('#busypsf').html(Math.round((PSFVALUE+1)) + "%");
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busypsfmin').click(function(){
					if (isDoubleClicked($(this))) return;           
					SCommand = "M220 S" + (PSFVALUE-1);
					$('#busypsf').html(Math.round((PSFVALUE-1)) + "%");
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyesfplus').click(function(){
					if (isDoubleClicked($(this))) return;
					SCommand = "M221 S" + (ESFVALUE+1);
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyesfmin').click(function(){
					if (isDoubleClicked($(this))) return;
					SCommand = "M221 S" + (ESFVALUE-1);
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyfsplus').click(function(){
					if (isDoubleClicked($(this))) return;
					FanSpeedValue = (255/100)*(FSVALUE+1);
					SCommand = "M106 S" + FanSpeedValue;
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });
				  $('.busyfsmin').click(function(){
					if (isDoubleClicked($(this))) return;
					FanSpeedValue = (255/100)*(FSVALUE-1);
					SCommand = "M106 S" + FanSpeedValue;
					$.ajax({
					  url: 'TerminalSubmit.php?PrinterState='+SCommand,
					  success:function(){
					  }
					});
				  });       
				$.getJSON("XYZData.php", function(GetXYZData){
					if(GetXYZData['UPDATEVALUES']['FS'] !== undefined)
					{ 
					  $('#FS').val(GetXYZData['UPDATEVALUES']['FS']*100);
					}
					if(GetXYZData['UPDATEVALUES']['PSF'] !== undefined)
					{ 
					  $('#PSF').val(GetXYZData['UPDATEVALUES']['PSF']*100);
					}
					if(GetXYZData['UPDATEVALUES']['ESF'] !== undefined)
					{ 
					  $('#ESF').val(GetXYZData['UPDATEVALUES']['ESF']*100);
					}
					$('#FanSpeedSubmit').click(function(){
						if (isDoubleClicked($(this))) return;
						//alert($('#FS').val())
						FanSpeedValue = (255/100)*($('#FS').val());
						SCommand = "M106 S" + FanSpeedValue;
						$.ajax({
						  url: 'TerminalSubmit.php?PrinterState='+SCommand,
						  success:function(){
						  }
						});
					  });
					$('#PrintSpeedSubmit').click(function(){
						if (isDoubleClicked($(this))) return;           
						PrintSpeedValue = $('#PSF').val();             
						SCommand = "M220 S" + PrintSpeedValue;
						$.ajax({
						  url: 'TerminalSubmit.php?PrinterState='+SCommand,
						  success:function(){
						  }
						});
					  });
					$('#ExtrudeSpeedSubmit').click(function(){
						if (isDoubleClicked($(this))) return;
						ExtrudeSpeedValue = $('#ESF').val();
						SCommand = "M221 S" + ExtrudeSpeedValue;
						$.ajax({
						  url: 'TerminalSubmit.php?PrinterState='+SCommand,
						  success:function(){
						  }
						});
					  });
					$('#terminalData').click(function(){
						$('#terminaltext').focus();
					});
				});
				
				function TerminalData(){
					var get_robot_terminal = {"id": "123", "method": "get_robot_terminal", "api_key": api_key, "robot": robot_name}
					$.ajax({
						type: "POST",
						url:robot_api_url,
						data: JSON.stringify(get_robot_terminal),
						success:function(Terminaldata){
							var TerminalDataLast = Terminaldata;

							var TerminalDataShow = '';							
							$.each( TerminalDataLast, function( TerminalDataKey, TerminalDataValue ) {
									if (TerminalDataValue['command'] == undefined)
									{
										TerminalDataShow = TerminalDataShow + TerminalDataValue['recv'] + "<br>";
									}
									else
									{
										TerminalDataShow = TerminalDataShow + TerminalDataValue['command'] + "<br>" + TerminalDataValue['recv'] + "<br>";
									}
								});
							$('#terminalData').html(TerminalDataShow);
							$('#terminalData').scrollTop($('#terminalData')[0].scrollHeight);
							
						 },
						 complete:function(){
							 setTimeout(function(){TerminalData();}, 500);
						 }
					 });
				 }
				TerminalData();
				
			});

			
		 },
	 });
 }
get_robot_help();
