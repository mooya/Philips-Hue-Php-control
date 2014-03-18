<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['FormAction']) &&$_POST['FormAction'] == "SaveScene")
{
	if ($_POST['EditMode'] == "0")
	{
		$Query		= "INSERT INTO scenes (scene_name, scene_order, timestamp, scene_image, page_id) VALUES ( '" .add($_POST['scene_name'])."',  999,'".time()."', 'default.png', '" .add($_POST['page_id'])."');";
		$Result		= mysqli_query($DB, $Query);
		$SceneID	= mysqli_insert_id($DB);
	
		if (is_uploaded_file($_FILES['file']['tmp_name']) ) 
		{ 
			@$userfile		= addslashes(fread (fopen ($_FILES["file"]["tmp_name"], "r"), filesize ($_FILES["file"]["tmp_name"])));
			$file_type 		= $_FILES["file"]["type"];
			$file_size 			= $_FILES["file"]["size"];
			$Extentie			= strtolower(substr(strrchr( $_FILES["file"]["name"], '.'), 1));
			$file_name		= $SceneID."_".sha1($_FILES["file"]["name"])."_original.".$Extentie;
			
			mysqli_query($DB, "UPDATE `scenes` SET `scene_image` = '".add($file_name)."' WHERE `scene_id` = ".(int)$SceneID) or die('Unable to execute query. '. mysqli_error($DB));
	
			copy($_FILES['file']['tmp_name'], "images/scenes/".$file_name); 
			list($width, $height, $type, $attr) = getimagesize("images/scenes/".$file_name);
		
			if ($width >200  || $height > 200  )
			{
				thumbgenerator ("images/scenes/".$file_name, "save", 100, 200,"images/scenes/".str_replace("original","big",$file_name));
			}
			else
			{
				copy("images/scenes/".$file_name,"images/scenes/".str_replace("original","big",$file_name));
			}
		}
	
		for ($i=0; $i < count($_POST['lights']); $i++)
		{
			$Light		=  $_POST['lights'][$i];
	
			$state		= $_POST['Light'][$Light]['\'state\''];
			$colormode	= $_POST['Light'][$Light]['\'colormode\''];
			$brightness	= $_POST['Light'][$Light]['\'brightness\''];
			$hue	= $_POST['Light'][$Light]['\'hue\''];
			$saturation	= $_POST['Light'][$Light]['\'saturation\''];
			$color_temp	= $_POST['Light'][$Light]['\'color_temp\''];
			if ($state == "on" && $brightness == 0)
			{
				$brightness =200;
			}
			
			
			$Query		= "INSERT INTO scene_settings (`scene_id`, `light_id`, `state`, `colormode`, `brightness`, `hue`, `saturation`, `color_temp`) VALUES ( ".(int)$SceneID.", ".(int)$Light.", ".(int)$state.", '".add($colormode)."', ".(int)$brightness.", ".(int)$hue.", ".(int)$saturation.", ".(int)$color_temp." );";
			$Result 	= mysqli_query($DB, $Query);
		}
		echo "<h1>Scene opgeslagen</h1>";
		die();
	}
	elseif ($_POST['EditMode'] == "1" && isset($_POST['EditScene'])) 
	{
		$SceneID = $_POST['EditScene'];
		$Query 		= "UPDATE scenes SET page_id  = '" .add($_POST['page_id'])."', scene_name = '" .add($_POST['scene_name'])."' WHERE scene_id= ".(int)$SceneID;
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		
		if (is_uploaded_file($_FILES['file']['tmp_name']) ) 
		{ 
			@$userfile		= addslashes(fread (fopen ($_FILES["file"]["tmp_name"], "r"), filesize ($_FILES["file"]["tmp_name"])));
			$file_type 		= $_FILES["file"]["type"];
			$file_size 			= $_FILES["file"]["size"];
			$Extentie			= strtolower(substr(strrchr( $_FILES["file"]["name"], '.'), 1));
			$file_name		= $SceneID."_".sha1($_FILES["file"]["name"])."_original.".$Extentie;
			
			mysqli_query($DB, "UPDATE `scenes` SET `scene_image` = '".add($file_name)."', `page_id`  = '".add($_POST['page_id'])."' WHERE `scene_id` = ".(int)$SceneID) or die('Unable to execute query. '. mysqli_error($DB));
	
			copy($_FILES['file']['tmp_name'], "images/scenes/".$file_name); 
			list($width, $height, $type, $attr) = getimagesize("images/scenes/".$file_name);
		
			if ($width >200  || $height > 200  )
			{
				thumbgenerator ("images/scenes/".$file_name, "save", 100, 200,"images/scenes/".str_replace("original","big",$file_name));
			}
			else
			{
				copy("images/scenes/".$file_name,"images/scenes/".str_replace("original","big",$file_name));
			}
		}
		
		
		
		$Query 				= "DELETE FROM  scene_settings WHERE scene_id = ".(int)$SceneID;
		$Result 				= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		for ($i=0; $i < count($_POST['lights']); $i++)
		{
			$Light				= $_POST['lights'][$i];
			$state				= $_POST['Light'][$Light]['\'state\''];
			$colormode		= $_POST['Light'][$Light]['\'colormode\''];
			$brightness		= $_POST['Light'][$Light]['\'brightness\''];
			$hue				= $_POST['Light'][$Light]['\'hue\''];
			$saturation		= $_POST['Light'][$Light]['\'saturation\''];
			$color_temp		= $_POST['Light'][$Light]['\'color_temp\''];
			
			if ($state == "on" && $brightness == 0)
			{
				$brightness =200;
			}
			
			$Query				= "INSERT INTO scene_settings (`scene_id`, `light_id`, `state`, `colormode`, `brightness`, `hue`, `saturation`, `color_temp`) VALUES ( ".(int)$SceneID.", ".(int)$Light.", ".(int)$state.", '".add($colormode)."', ".(int)$brightness.", ".(int)$hue.", ".(int)$saturation.", ".(int)$color_temp." );";
			$Result 			= mysqli_query($DB, $Query);
		}
		echo "<h1>Scene bijgewerkt</h1>";
		die();
	}
}



function PrintRow($i)
{
	global $_cfg;
	global $SelectedLights;
	global $EditMode;
	//Verborgen lampen niet weergeven indien er geen groep is opgegeven.
	if ((!in_array($i,$_cfg["HideLights"]) && $_cfg["ShowGroup"] == 0)  || $_cfg["ShowGroup"] == 1)
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$i;
		$Light			= GetJSON($Url);
	
		$Img			= "";
		if ($Light["state"]["reachable"] == false)
		{ 
			$Img = "<img src=\"images/alert.png\" title=\"Onbereikbaar\" >"; 
		}
			
		$Reachable		= $Light["state"]["reachable"];
		$Name				= $Light["name"];
		$Brightness		= $Light["state"]["bri"];
		if (isset($Light["state"]["hue"]))
		{
			$Hue			= $Light["state"]["hue"];
			$Saturation	= $Light["state"]["sat"];
			$Colormode	= $Light["state"]["colormode"];
		}
		else
		{
			$Colormode	= "ct";
			$Hue			= -1;
			$Saturation	= 0;
		}
		
		if (isset($Light["state"]["ct"]))
		{
			$ColorTemp	= $Light["state"]["ct"];
		}
		else
		{
			$ColorTemp	= 0;
		}
		
		//$SelectedLights
		
		$Selected 	= "checked";
		if ($EditMode == 1 && !isset($SelectedLights[$i]))
		{
			$Selected 	= "";
		}
		
		 $StatusImg		= "<img src=\"images/button-off.png\" style=\"cursor:pointer\" width=\"50\" height=\"17\" id=\"Light_".$i."_img\" onclick=\"ToggleLightAndSaveState(".$i.")\">";
		 if ($Light["state"]["on"] == 1)
		 {
			 $StatusImg		= "<img src=\"images/button-on.png\" style=\"cursor:pointer\" width=\"50\" height=\"17\" id=\"Light_".$i."_img\" onclick=\"ToggleLightAndSaveState(".$i.")\">";
		 }

		$Fields = "<input type=\"hidden\" name=\"Light[".$i."]['state']\" value=\"".$Light["state"]["on"]."\" id=\"light_".$i."_state\">";
		$Fields .= "<input type=\"hidden\" name=\"Light[".$i."]['colormode']\" value=\"".$Colormode."\" id=\"light_".$i."_colormode\">";
		$Fields .= "<input type=\"hidden\" name=\"Light[".$i."]['brightness']\" value=\"".$Brightness."\" id=\"light_".$i."_brightness\">";
		$Fields .= "<input type=\"hidden\" name=\"Light[".$i."]['hue']\" value=\"".$Hue."\" id=\"light_".$i."_hue\">";
		$Fields .= "<input type=\"hidden\" name=\"Light[".$i."]['saturation']\" value=\"".$Saturation."\" id=\"light_".$i."_saturation\">";
		$Fields .= "<input type=\"hidden\" name=\"Light[".$i."]['color_temp']\" value=\"".$ColorTemp."\" id=\"light_".$i."_color_temp\">";
		
		
		echo "<tr>\n";
		echo "      <td><input type=\"checkbox\" value=\"".$i."\" name=\"lights[]\" ".$Selected."></td>\n";
		echo "      <td style=\"text-align: left;\">".$StatusImg.$Img.$Fields."</td>\n";
		echo "      <td id=\"".$i."\">".$Light["name"]."\n";
		echo "<div id=\"slider_".$i."\" style=\"width:250px; height: 18px;\"></div>";
		echo "<script>\n";
		echo " $(function() {\n";
		echo "          $( \"#slider_".$i."\" ).slider({ \n";
		echo "		  	max: 255, \n";
		echo "			value: ".$Brightness.",\n";
		echo "			slide: function( event, ui ) {\n";
		echo "         		 ChangeBrightnessAndSaveState(".$i.",ui.value);\n";
		echo "    		  }\n";
		echo "			});\n";
		echo "        });\n";
		
		echo "</script>\n";
		echo "</td>";
		echo "</tr>\n";
		
	
		$Output['Number'] 		= $i;
		$Output['Reachable'] 	= $Reachable;
		$Output['Name'] 			= $Name;
		$Output['Brightness'] 	= $Brightness;
		$Output['Hue'] 			= $Hue;
		$Output['Saturation'] 	= $Saturation;
		$Output['Colormode'] 	= $Colormode;
		$Output['ColorTemp'] 	= $ColorTemp;
		return $Output;
		
	}
}


if ( !$_cfg["UseMysql"] 	)
{
	echo "dit werkt alleen indien MySQL is ingeschakeld.";
}

if (!isset($_GET['PageID']) )
{
	$Result 	= mysqli_query($DB, "SELECT page_id FROM scene_pages ORDER BY page_order");
	
	if (mysqli_num_rows($Result) == 0) 
	{			
		//Er bestaat nog geen pagina. 1tje aanmaken
		$Query		= "INSERT INTO scene_pages (page_name) VALUES ( 'Pagina 1' );";
		$Result		= mysqli_query($DB, $Query);
		$_GET['PageID'] 	= mysqli_insert_id($DB);
	}
	else
	{
		$Pages	 = mysqli_fetch_assoc($Result);
		$_GET['PageID'] = $Pages['page_id'];
	}
}
	
if (isset($_GET['SceneID']) )
{
	$Result 	= mysqli_query($DB, "SELECT scene_name, scene_image, page_id FROM scenes WHERE scene_id = ".(int)$_GET['SceneID']);
	if (mysqli_num_rows($Result) == 0) 
	{			
		die('ongeldige scene gekozen.');
	}
	$scenes	 = mysqli_fetch_assoc($Result);
	$scene_name = $scenes['scene_name'];
	$scene_image = $scenes['scene_image'];
	$_GET['PageID'] = $scenes['page_id'];
	$EditMode	= 1;
	
	$Query    	= "SELECT light_id  FROM scene_settings WHERE scene_id = ".(int)$_GET['SceneID'];
	$Result 	= mysqli_query($DB, $Query);
	$SelectedLights	= array();
	while ($Scene = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		$SelectedLights[$Scene['light_id']] = 1;

	}
}
else
{
	
	$scene_image = "spacer.gif";
	$SelectedLights	= array();
	$scene_name = "Nieuwe scene";
	$EditMode	= 0;
	$_GET['SceneID'] = 0;
}

?>
<style>
.SmallTable {margin: 0; padding:0; border:none; }

</style>

<script>
var coordinates = function(element,light) 
{
    element = $(element);
    var top = element.position().top;
    var left = element.position().left;
	
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeLightByCoorReturn&light_id='+light+'&left='+left+'&top='+top,true);
	
		xmlhttp.onreadystatechange=function() 
		{
			 if (xmlhttp.readyState==4) 
			 {
				 $('#results2').html(xmlhttp.responseText)
			 }
		}
	
	xmlhttp.send();

	
}

function SaveScene()
{
	var scene_name=prompt("Enter a scene name","<?= $scene_name?>");
	if (scene_name!=null)
	{
		document.SelectForm.scene_name.value = scene_name
		document.SelectForm.FormAction.value = "SaveScene";
		document.SelectForm.submit();
	}
}


function ToggleLightAndSaveState(lightnumber)
{
	if ($('#Light_'+lightnumber+'_img').attr('src') == 'images/button-on.png')
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
		xmlhttp.send('{"on": false}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-off.png');
		$('#light_'+lightnumber+'_state').val(0);
	}
	else
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
		xmlhttp.send('{"on": true}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-on.png');
		$('#light_'+lightnumber+'_state').val(1);
	}
}


function ChangeBrightnessAndSaveState(lightnumber,brightness)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('PUT', 'http://<?=$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"] ?>/lights/'+lightnumber+'/state',true);
	if (brightness == 0)
	{
		//uitzetten bij brightness 0
		xmlhttp.send('{"on": false}');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-off.png');
		$('#light_'+lightnumber+'_state').val('0');
		$('#light_'+lightnumber+'_brightness').val('0');
	}
	else
	{
		xmlhttp.send('{"on": true,"bri": '+brightness+' }');
		$('#Light_'+lightnumber+'_img').attr('src', 'images/button-on.png');
		$('#light_'+lightnumber+'_state').val(1);
		$('#light_'+lightnumber+'_brightness').val(brightness);
	}
}
</script>
<form name="SelectForm" method="post" enctype="multipart/form-data">
<input type="hidden" name="FormAction" value="" />
<table width="1000" style="border:none; margin:0;padding:0;">
  <tr>
    <td colspan="2"><div style="background-image:url(images/scenes/<?= str_replace("original","big",$scene_image)?>); background-repeat:no-repeat; height:100px; background-position:right;">
	<h1><?php if ($EditMode == 1) { echo "Scene bewerken: ".$scene_name; } else { echo "Scene toevoegen"; }  ?></h1>
	<p>Vink de lampen aan die je in de Scene verwerkt wilt hebben. <br />Stel ook de stand in zoals je deze in de scene opgeslagen wilt hebben.</p>
</div></td>
  </tr>
  <tr>
    <td colspan="2"></td>
  </tr>
  <tr>
    <td>
    <table width="1000" border="1">
  
  	<thead>
<?php
	$Query    	= "SELECT group_id, group_name, group_order FROM groups ORDER BY group_order";
	$Result 	= mysqli_query($DB, $Query);
	echo "    <tr>\n";
	echo "      <th colspan=\"3\">\n";	
	echo "<select name=\"groupname\" style=\"width:100%\" onchange=\"document.SelectForm.submit()\"> \n";
	echo "<option value=\"all\" ".CheckValues($_POST['groupname']  ,"all"," selected","").">Alles</option>\n";
	
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		echo "<option value=\"".$row["group_id"]."\" ".CheckValues($_POST['groupname']  ,$row["group_id"]," selected","").">".$row["group_name"]."</option>\n";
	}
	
	echo "</select>\n";
	echo "</th>\n";
	echo "    </tr>	\n";
	echo "    <tr>\n";
	echo "      <th colspan=\"3\">\n";
	
	$Query    	= "SELECT page_id, page_name, page_order FROM scene_pages ORDER BY page_order";
	$Result 	= mysqli_query($DB, $Query);
	
	echo "<select name=\"page_id\" style=\"width:100%\"> \n";
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		echo "<option value=\"".$row["page_id"]."\" ".CheckValues($_GET['PageID']  ,$row["page_id"]," selected","").">".$row["page_name"]."</option>\n";
	}
	echo "</select>\n";
	echo "</th>\n";
	echo "    </tr>	\n";
	echo "    <tr>\n";
	echo "      <th colspan=\"3\"><input type=\"file\" name=\"file\" /></th>\n";
	echo "    </tr>	\n";
	
	
?>

	</thead>
  <?php
	if ($_POST['groupname']  == "all")
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
		$Config		= GetJSON($Url);
		$_cfg["ShowGroup"] =0;
		for ($i=1; $i<count($Config); $i++)
		{
			$Status[$i] = PrintRow($i);
		}
	}
	else
	{
		$Query    	= "SELECT light_id, light_order FROM group_members WHERE group_id = ".(int)$_POST['groupname']." ORDER BY light_order";
		$Result 	= mysqli_query($DB, $Query);
		$_cfg["ShowGroup"] =1;
		$i				= 1;
		 while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
		{ 
			$Status[$i] = PrintRow($row['light_id']);
			$i++;
		}
	}
?></table>
<input type="button" name="SaveButton" value="Scene opslaan" onclick="SaveScene()" />

<input type="hidden" name="scene_name" value="" />
<input type="hidden" name="EditMode" value="<?= $EditMode?>" />
<input type="hidden" name="EditScene" value="<?= $_GET['SceneID']?>" />

    <td width="750">
    	<div id="containment">

            <div id="containment_whites">
<?php
		for ($i=1; $i<= count($Status); $i++)
		{
			//Bijv een Living living white plug, deze kan geen kleur tinten geven.
			if (isset($Status[$i]['Hue']) && $Status[$i]['Hue'] == -1)
			{
				if ($_cfg["HideNonHueLights"] == 0)
				{
					echo "				<div id=\"draggable_".$Status[$i]['Number']."\" title=\"".$Status[$i]['Name']."\"></div>\n";
				}
			}
		}
?>            </div>
            
            <div id="containment_color">
<?php
		for ($i=1; $i<= count($Status); $i++)
		{
			//Bijv een Living colors lamp, deze kan geen witte tinten geven.
			if ($Status[$i]['Hue'] <> -1  && $Status[$i]['ColorTemp'] ==  0)
			{
				echo "				<div id=\"draggable_".$Status[$i]['Number']."\"  title=\"".$Status[$i]['Name']."\"></div>\n";
			}
		}
?>            </div>
            
<?php
		for ($i=1; $i<= count($Status); $i++)
		{
			if (isset($Status[$i]['Colormode'])&& isset($Status[$i]['Hue']))
			{
				if ( $Status[$i]['Hue'] <> -1 && $Status[$i]['ColorTemp'] <> 0)
				{
					echo "			<div id=\"draggable_".$Status[$i]['Number']."\" title=\"".$Status[$i]['Name']."\"></div>\n";
				}
			}
		}
?>
		</div>
    </td>
  </tr>
</table>

<div id="results"></div>​
<div id="results2"></div>​

<style>
	#containment { width:750px; height:750px; position: relative;  margin: 0; background-image:url(images/large_hue_bg.png)}
	#containment_whites { width:750px; height:250px; position: relative; }
	#containment_color { width:750px; height:500px; position: relative;  }
<?php
		for ($i=1; $i<= count($Status); $i++)
		{
			if (isset($Status[$i]['Number']))
			{
				//Postitie nog berekenen adhv:
					//$Status[$i]['Hue']   0/65535
					//$Status[$i]['ColorTemp']   0/65535
					//$Status[$i]['Saturation']  0/255
					//$Status[$i]['Colormode']  //hs, xy, ct
				
				$DivWith	= 700;
				$DivWhiteHeight = 220;
				$DivColorHeight = 500;
				
				$Top			= 0;
				$Left 		= 0;
	
				if ($Status[$i]['Hue'] == -1)
				{
					//lamp die geen kleuren support, dus weergeven in bovenste balk op random plek
					$Left			= rand(0,$DivWith);
					$Top				= rand(0,$DivWhiteHeight);
					
				}
				elseif ($Status[$i]['Hue'] <> -1 && $Status[$i]['ColorTemp'] ==  0)
				{
					//lamp die geen witte tinten support, dus weergeven in onderste balk
					$Min		= 0;
					$Max	= 450;
					$Left	= round(($Status[$i]['Hue'] /65535)*$DivWith);
					$Top		= round(($Status[$i]['Saturation'] /255)*($Max-$Min));
					
					$Top		= round(($Status[$i]['Saturation'] /255)*($Max-$Min));
					$Top		= ($Top+$Min) ;
				}
				elseif ( $Status[$i]['Colormode'] == "xy" && $Status[$i]['ColorTemp'] <> 0)
				{
					//hue lamp welke volledige kleuren kan, en weergeven in kleuren deel,
					//maar in XY mode.. 
					//is nog onbekend hoe deze gegevens weergegeven moeten worden..
					//gegevens overgenomen van de COLORMODE HS hier onder.
					$Left	= round(($Status[$i]['Hue'] /65535)*$DivWith);
					$Min		= 230;
					$Max	= 700;
					
					$Top		= round(($Status[$i]['Saturation'] /255)*($Max-$Min));
					$Top		= ($Top+$Min) ;
				}
				elseif ( $Status[$i]['Colormode'] == "hs")
				{
					//hue lamp welke volledige kleuren kan, en weergeven in kleuren deel
					$Left	= round(($Status[$i]['Hue'] /65535)*$DivWith);
					$Min		= 230;
					$Max	= 700;
					
					$Top		= round(($Status[$i]['Saturation'] /255)*($Max-$Min));
					$Top		= ($Top+$Min) ;
				}
				elseif ( $Status[$i]['Colormode'] == "ct")
				{
					//hue lamp welke volledige kleuren kan, en weergeven in kleuren temperatuur deel
					//color temp is waarde tussen 153 (6500K) to 500 (2000K).
					$Left	= round((   ($Status[$i]['ColorTemp']-153) /347  )*$DivWith); //(347 = 500-153)				
					$Top		= rand(0,$DivWhiteHeight);
				}

				echo "	#draggable_".$Status[$i]['Number']." { width: 50px; height: 50px; background-image:url(images/colorpicker-50px.png); border: none; top:".$Top."px; left:".$Left."px; position: absolute;}\n";
			}
		}
?>
</style>


<script>
$(function() {
<?php
		$ColorLights	= 0;
		for ($i=1; $i<= count($Status); $i++)
		{
			if (isset($Status[$i]['Number']))
			{
				if ($Status[$i]['Hue'] == -1)
				{
					$containment = "containment_whites";
				}
				elseif ($Status[$i]['Colormode'] == "xy")
				{
					$ColorLights++;
					$containment = "containment_color";
				}
				else
				{
					$ColorLights++;
					$containment = "containment";
				}
				
 				if ($Status[$i]['Hue'] == -1 && $_cfg["HideNonHueLights"] ==1)
				{
					//Lamp niet weergeven in overzicht
				}
				else
				{
					echo "	$( \"#draggable_".$Status[$i]['Number']."\" ).draggable({ containment: \"#".$containment."\", scroll: false,  start: function() {coordinates('#draggable_".$Status[$i]['Number']."',".$Status[$i]['Number'].");},  stop: function() {coordinates('#draggable_".$Status[$i]['Number']."',".$Status[$i]['Number'].");} })\n";
				}
			}
		}
?>	
	$( document ).tooltip();
<?php
if ($ColorLights == 0) 
{
	//color picker verkleinen naar 250 pixels omdat er geen kleuren lampen zijn in de groep.
	echo "$(\"#containment\").css({height:'250px'})\n "; 
} 
?>
});

</script>

</form>