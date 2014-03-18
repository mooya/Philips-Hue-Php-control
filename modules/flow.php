<h1>Flow colors</h1>
<script>

function getRandomInt (min, max) 
{
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function Looplight(lightnumber,delay)
{
	times		= document.form.times.value;

	if (times >0 )
	{
		hue_min = 20000;
		hue_max = 65535;//max: 65535
		
		saturation_min = 200;
		saturation_max = 254;
	
		hue = getRandomInt (hue_min, hue_max);
		saturation = getRandomInt (saturation_min, saturation_max);
		
		$('#StopBtn').show();
		$('#StartBtn').hide();
		$('#Sliders').show();
		
		$(function() {
			  $( "#slider_hue" ).slider({ 
				max: 65535, 
				value: hue
				});
			});

		$('#slider_hue').slider( 'disable');
		$(function() {
			  $( "#slider_sat" ).slider({ 
				max: 255, 
				value: saturation
				});
			});

		$('#slider_hue').slider( 'disable');
		
		ChangeHueSaturation(lightnumber,hue,saturation);	
		times = times -1;
		document.form.times.value = times

		setTimeout(function()
		{
			Looplight(lightnumber,delay);	
		},delay);
	}
	else
	{
		$('#Sliders').hide( "slow", function() {});
		$('#StopBtn').hide();
		$('#StartBtn').show();
	}
	
}
/*
function Looplight2(lightnumber,delay,times)
{
	hue_min = 20000;
	hue_max = 65535;//max: 65535
	//hue_min = 0;
	//hue_max = 65535;//max: 65535
	
//	saturation_min = 175;
	saturation_min = 200;
	saturation_max = 254;

	hue = getRandomInt (hue_min, hue_max);
	saturation = getRandomInt (saturation_min, saturation_max);
	
	
	$('#Sliders').show();
	
	$(function() {
          $( "#slider_hue" ).slider({ 
		  	max: 65535, 
			value: hue
			});
        });
	$('#slider_hue').slider( 'disable');
	$(function() {
          $( "#slider_sat" ).slider({ 
		  	max: 255, 
			value: saturation
			});
        });
	$('#slider_hue').slider( 'disable');
	
	ChangeHue(lightnumber,hue,saturation);	
	times = times -1;
	
	if (times >0 )
	{
		setTimeout(function()
		{
			Looplight(lightnumber,delay,times);	
		},delay);
	}
	else
	{
	$('#Sliders').hide( "slow", function() {});
	}
	
}
*/
</script>
<form name="form" method="post" action="">
<table>
<thead>
<tr>
<th style="text-align: left; width:250px">Setting</th>
<th style="text-align: left; width:750px">Value</th>
</tr>
</thead>
<tbody>
<tr>
<td>Lamp</td>
<td><select name="lightnumber" id="lightnumber" style="width:250px">
<?php
	$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
	$Config		= GetJSON($Url);
	
	for($i = 1; $i  <= Count($Config); $i++)
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$i;
		$Light			= GetJSON($Url);
		if (isset($Light["state"]["hue"]))
		{	
			echo "  <option value=\"".$i."\">".$Config[$i]["name"]."</option>\n";
		}
	}
?></select></td>
</tr>
<tr>
<td>Pauze tussen standen</td>
<td><input name="delay" type="text" value="5000" style="width:250px"> (ms)</td>
</tr>
<tr>
<td>Aantal keer</td>
<td><input name="times" type="text" value="10" style="width:250px"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>
	<div id="StartBtn"><input type="button" name="button" value="Change Mood" style="width:250px" onclick="Looplight(document.form.lightnumber.value,document.form.delay.value, document.form.times.value)" /></div>
	<div id="StopBtn" style="display:none"><input type="button" name="button" value="Stop" style="width:250px" onclick="document.form.times.value=0" /></div>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><div id="Sliders" style="display: none">
	<div id="slider_hue" style="width:600px; height: 25px; background-image:url(images/hue-600.jpg); margin-top: 10px;"></div>
	<div id="slider_sat" style="width:600px; height: 25px;  margin-top: 10px;"></div>
    </div>
</td>
</tr>
</tbody>
</table>
</form>