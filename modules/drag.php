<?php
function PrintRow($i)
{
	global $_cfg;
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
		echo "<tr>\n";
		echo "      <td id=\"".$i."\">".$Light["name"]." ".$Img."\n";
		echo "<div id=\"slider_".$i."\" style=\"width:250px; height: 18px;\"></div>";
		echo "<script>\n";
		echo " $(function() {\n";
		echo "          $( \"#slider_".$i."\" ).slider({ \n";
		echo "		  	max: 255, \n";
		echo "			value: ".$Brightness.",\n";
		echo "			slide: function( event, ui ) {\n";
		echo "         		 ChangeBrightness(".$i.",ui.value);\n";
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




?><h1>Drag and drop</h1>
<script>
var coordinates = function(element,light) 
{
    element = $(element);
    var top = element.position().top;
    var left = element.position().left;
 //   $('#results').text('Light: '+ light + ' X: ' + left + ' ' + 'Y: ' + top);
	
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeLightByCoor&light_id='+light+'&left='+left+'&top='+top,true);
	
		xmlhttp.onreadystatechange=function() 
		{
			 if (xmlhttp.readyState==4) 
			 {
			//	 $('#results2').text(xmlhttp.responseText)
			 }
		}
	
	xmlhttp.send();

	
}



</script>

<table width="1000" class="TableMargin">
  <tr>
    <td>
    <form name="SelectForm" action="">
<input name="Page" value="drag" type="hidden" />
    <table width="1000" border="1" class="TableMargin">

  	<thead>
    <tr>
      <th><?php


if ( $_cfg["UseMysql"] 	)
{
 	
	$Query    	= "SELECT group_id, group_name, group_order FROM groups ORDER BY group_order";
	$Result 	= mysqli_query($DB, $Query);
	
	echo "<select name=\"groupname\" style=\"width:100%\" onchange=\"document.SelectForm.submit()\"> \n";
	echo "<option value=\"all\" ".CheckValues($_GET['groupname']  ,"all"," selected","").">Alles</option>\n";
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		echo "<option value=\"".$row["group_id"]."\" ".CheckValues($_GET['groupname']  ,$row["group_id"]," selected","").">".$row["group_name"]."</option>\n";
	}
	echo "</select>\n";
}
else
{
	echo "<h2>Alle lampen</h2>";
}
?></th>
    </tr>
	</thead>
  <?php
	if ($_GET['groupname']  == "all")
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
		$Query    	= "SELECT light_id, light_order FROM group_members WHERE group_id = ".(int)$_GET['groupname']." ORDER BY light_order";
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
</form>

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
{//color picker verkleinen naar 250 pixels omdat er geen kleuren lampen zijn in de groep.
	echo "$(\"#containment\").css({height:'250px'})\n "; 
} 
?>
});

</script>