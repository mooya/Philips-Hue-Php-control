<?php

session_start(); 

include 'includes/sql_settings.php'; 
include 'includes/settings.php'; 
include 'includes/functions.php'; 

if (isset($_GET['Action']) && $_GET['Action']  == "Logoff" )
{
	$_SESSION['LoginCheck'] = 0;
}

//Gebruiker automatisch in laten loggen indien IP adres matched.
if ($_cfg["Login"]['IPMatch']  <> "" && substr($_SERVER['REMOTE_ADDR'],0,strlen($_cfg["Login"]['IPMatch'] )) == $_cfg["Login"]['IPMatch'] )
{
	$_SESSION['LoginCheck'] = 1;
}

if (!LoginCheck())
{
	header("Location: login.php"); 
	exit;	
}

if ($_cfg["CheckHash"] )
{
	$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"];
	$Info			= GetJSON($Url);
	if (isset($Info[0]["error"]["description"]))
	{
		$_GET['Page'] = "add_user";
	}
}


if (!isset($_GET['Page'])) 	{ $_GET['Page']  = "home"; } 

switch ($_GET['Page']) 
{
	case "home":
		$include = "home.php";
	break;

	case "lights":
	
		if (!isset($_GET['groupname']) && $_cfg["UseMysql"] )
		{
			$Result 	= mysqli_query($DB, "SELECT group_id FROM groups ORDER BY group_order");
			
			if (mysqli_num_rows($Result) == 0) 
			{			
				$_GET['groupname'] = "all";
			}
			else
			{
				$Groups	 = mysqli_fetch_assoc($Result);
				$_GET['groupname'] = $Groups['group_id'];
			}
		}
		elseif (!isset($_GET['groupname']) && !$_cfg["UseMysql"] )
		{
			$_GET['groupname'] = "all";
		}
		if ($_cfg["UseMysql"] && $_GET['groupname'] == "addgroup")
		{
			$include = "add_group.php";
		}
		else
		{
			$include = "lights.php";
		}
	break;

	case "groups":
		$include = "groups.php";
	break;

	case "group_detail":
		$include = "group_detail.php";
	break;


	case "bridge":
		$include = "bridge.php";
	break;

	case "schedules":
	$include = "schedules.php";
	break;

	case "sunrise":
	$include = "sunrise.php";
	break;

	case "debug":
	$include = "debug.php";
	break;

	case "add_scene":
		if (!isset($_POST['groupname']))
		{
			$_POST['groupname'] = "all";
		}
	$include = "add_scene.php";
	break;
	
	case "add_user":
	$include = "add_user.php";
	break;
	
	case "drag":
		if (!isset($_GET['groupname']))
		{
			$_GET['groupname'] = "all";
		}
		$include = "drag.php";
	break;

	case "flow":
	if (!isset($_GET['groupname']))
	{
		$_GET['groupname'] = "all";
	}
	$include = "flow.php";
	break;

	default:
	$include = "home.php";
}


?><!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Hue</title>
<base href="<?= $_cfg["WebsiteURL"] ?>" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script src="js/touch-punch/jquery.ui.touch-punch.min.js"></script>

<script>
$(function()
{
	$( document ).tooltip();
});

function ShowMessage(message)
{
	$( "#MessageDiv" ).html(message).slideDown();
	setTimeout(function()   {    $('#MessageDiv').slideUp();  }, 5000);
}

function ChangeBrightness(lightnumber,brightness)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	if (brightness == 0)
	{
		//uitzetten bij brightness 0
		xmlhttp.send('{"on": false}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-off.png');
	}
	else
	{
		xmlhttp.send('{"on": true,"bri": '+brightness+' }');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-on.png');
	}
}
function ChangeHue(lightnumber,hue)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	xmlhttp.send('{"on": true,"hue": '+hue+' }');
}

function ChangeColorTemp(lightnumber,temp)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	xmlhttp.send('{"on": true,"ct": '+temp+' }');
}

function ChangeHueSaturation(lightnumber,hue,saturation)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	xmlhttp.send('{"on": true,"hue": '+hue+',"transitiontime":30 ,"sat": '+saturation+'}');
}

function ChangeSaturation(lightnumber,sat)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	xmlhttp.send('{"on": true,"sat": '+sat+' }');
}

function ToggleLight(lightnumber)
{
	if ($('#Light_'+lightnumber+'_img').attr('src') == 'images/button-on.png')
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
		xmlhttp.send('{"on": false}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-off.png');
	}
	else
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
		xmlhttp.send('{"on": true}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-on.png');
	}
}

function ToggleColormode(light)
{
	if ($('#Colormode_'+light+'_img').attr('src') == 'images/button-hue.png')
	{
		$('#Sliders_1_'+light).hide();
		$('#Sliders_2_'+light).show();
		$('#Colormode_'+light+'_img').attr('src', 'images/button-temp.png');
	}
	else
	{
		$('#Sliders_1_'+light).show();
		$('#Sliders_2_'+light).hide();
		$('#Colormode_'+light+'_img').attr('src', 'images/button-hue.png');
	}

}

</script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="page">

    <div class="container content-container">

		<div id="MessageDiv"></div>

        <div class="row">
        <div id="menu"> 
      	      <ul class="navigatie">   	
      	      <li><a  class="first" href="<?= $_cfg["WebsiteURL"] ?>home" >Home</a></li>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>lights">Lampen</a></li>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>drag">Drag and drop</a></li>
<?php if ( $_cfg["UseMysql"] ) { ?>      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>groups">Groepen</a></li><?php } ?>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>bridge">Bridge info</a></li>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>flow" >Flow</a></li>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>schedules">Schedules</a></li>
      	      <li><a  href="<?= $_cfg["WebsiteURL"] ?>sunrise">Sunrise</a></li>
      	      <li><a  href="http://developers.meethue.com/1_lightsapi.html" target="_blank">API info</a></li>
      	      <li><a  <?php if ($_cfg["Login"]['Username'] == "") { ?>class="last" <?php } ?>href="<?= $_cfg["WebsiteURL"] ?>debug">Debug</a></li>
<?php if ($_cfg["Login"]['Username'] <> "") { ?>      	      <li><a class="last" href="<?= $_cfg["WebsiteURL"] ?>?Action=Logoff">Uitloggen</a></li><?php } ?>
      	      </ul>
        </div> 
        <div class="hue-main-content" data-role="main-content">

<?php include( "modules/".$include )?>

            </div>

        </div>


    </div>
<?php
/*
    <footer class="site-footer hue-stripes">

    	<img class="hue-color-line-footer" src="http://www.meethue.com/public/images/bg/hue-color-line.png">

    </footer>
*/
?>
</div>


</body>
</html>
