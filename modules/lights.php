<h1>Lampen overzicht</h1>
<?php
function PrintArrayBasic($item, $key)
{
	global $_cfg;
	//Verborgen lampen niet weergeven indien er geen groep is opgegeven.
	if ((!in_array($key,$_cfg["HideLights"]) && $_cfg["ShowGroup"] == 0)  || $_cfg["ShowGroup"] == 1)
	{
		//Key is het lamp nummer. Extra info ophalen
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$key;
		$Light			= GetJSON($Url);

		$Img			= "";
		if ($Light["state"]["reachable"] == false)
		{ 
			$Img = "<img src=\"images/alert.png\" title=\"Onbereikbaar\" >"; 
		}
		
		$Brightness	= $Light["state"]["bri"];
		if (isset($Light["state"]["hue"]))
		{
			$Hue			= $Light["state"]["hue"];
			$Saturation	= $Light["state"]["sat"];
			$Colormode	= $Light["state"]["colormode"];
			
			if ($Colormode		== "ct")
			{
				$Slider1		= " style=\"display: none\"";
				$Slider2		= "";
				$ButtonImg	= "<br><img src=\"images/button-temp.png\" width=\"74\" height=\"25\" id=\"Colormode_".$key."_img\" title=\"Kleuren modus wisselen\" onclick=\"ToggleColormode(".$key.")\" style=\"cursor:pointer\">";
			}
			else
			{
				$Slider1		= "";
				$Slider2		= " style=\"display: none\"";
				$ButtonImg	= "<br><img src=\"images/button-hue.png\" width=\"74\" height=\"25\" id=\"Colormode_".$key."_img\" title=\"Kleuren modus wisselen\" onclick=\"ToggleColormode(".$key.")\" style=\"cursor:pointer\">";
			}
		}
		else
		{
			$Hue	= -1;
			$ButtonImg = "";
		}
		
		if (isset($Light["state"]["ct"]))
		{
			$ColorTemp	= $Light["state"]["ct"];
		}
		else
		{
			$ColorTemp	= 0;
			$ButtonImg	= "";//geen toggle knop
		}

		 
		 $StatusImg		= "<img src=\"images/button-off.png\" style=\"cursor:pointer\" width=\"74\" height=\"25\" id=\"Light_".$key."_img\" onclick=\"ToggleLight(".$key.")\">";
		 if ($Light["state"]["on"] == 1)
		 {
			 $StatusImg		= "<img src=\"images/button-on.png\" style=\"cursor:pointer\" width=\"74\" height=\"25\" id=\"Light_".$key."_img\" onclick=\"ToggleLight(".$key.")\">";
		 }
		echo "    <tr>\n";
		echo "      <td style=\"text-align: left;\">". $Light["name"]." <i>".$Img."<br />".$Light["type"]."</i></td>\n";
		echo "      <td style=\"text-align: left;\">".$StatusImg.$ButtonImg."</td>\n";
		echo "      <td style=\"text-align: left;\">\n";
		echo "      	<div id=\"slider_".$key."\" style=\"width:600px; height: 25px;\"></div>\n";
		
		if ($Hue <> -1)
		{
			echo "      	<div id=\"Sliders_1_".$key."\"".$Slider1.">\n";
			echo "      		<div id=\"slider_hue_".$key."\" style=\"width:600px; height: 25px; background-image:url(images/hue-600.jpg); margin-top: 10px;\"></div>\n";
			echo "      		<div id=\"slider_sat_".$key."\" style=\"width:600px; height: 25px;  margin-top: 10px;\"></div>\n";
			echo "      	</div>\n";
			echo "      	<div id=\"Sliders_2_".$key."\"".$Slider2.">\n";
			echo "      		<div id=\"slider_temp_".$key."\" style=\"width:600px; height: 25px; background-image:url(images/white-tones-600.png);  margin-top: 10px;\"></div>\n";
			echo "      	</div>\n";
		}

		echo "<script>\n";
	 	echo " $(function() {\n";
		echo "          $( \"#slider_".$key."\" ).slider({ \n";
		echo "		  	max: 255, \n";
		echo "			value: ".$Brightness.",\n";
		echo "			slide: function( event, ui ) {\n";
		echo "         		 ChangeBrightness(".$key.",ui.value);\n";
		echo "    		  }\n";
		echo "			});\n";
		echo "        });\n";
		
		if ($Hue <> -1)
		{
			echo " $(function() {\n";
			echo "          $( \"#slider_hue_".$key."\" ).slider({ \n";
			echo "		  	max: 65535, \n";
			echo "			value: ".$Hue.",\n";
			echo "			slide: function( event, ui ) {\n";
			echo "         		 ChangeHue(".$key.",ui.value);\n";
			echo "    		  }\n";
			echo "			});\n";
			echo "        });\n";

			echo " $(function() {\n";
			echo "          $( \"#slider_sat_".$key."\" ).slider({ \n";
			echo "		  	max: 255, \n";
			echo "			value: ".$Saturation.",\n";
			echo "			slide: function( event, ui ) {\n";
			echo "         		 ChangeSaturation(".$key.",ui.value);\n";
			echo "    		  }\n";
			echo "			});\n";
			echo "        });\n";

			echo " $(function() {\n";
			echo "          $( \"#slider_temp_".$key."\" ).slider({ \n";
			echo "		  	min: 153, \n";
			echo "		  	max: 500, \n";
			echo "			value: ".$ColorTemp.",\n";
			echo "			slide: function( event, ui ) {\n";
			echo "         		 ChangeColorTemp(".$key.",ui.value);\n";
			echo "    		  }\n";
			echo "			});\n";
			echo "        });\n";

		}
		echo "</script></td>\n";
		echo "    </tr>\n";
	}
}


if ( $_cfg["UseMysql"] 	)
{
	//Groepen ophalen uit de DB
	CreateLightGroups ($_GET['groupname']);
}
else
{
	echo "<h2>Alle lampen</h2>";
}
?>
<table>
  <thead>
    <tr>
      <th>Naam</th>
      <th>Status</th>
      <th>Settings</th>
    </tr>
  </thead>
  <tbody>
<?php

if ( $_cfg["UseMysql"]  && isset($_GET['groupname'])	 && $_GET['groupname'] <> "all" )
{
	//opgegeven groep ophalen
	$Query    	= "SELECT light_id, light_order FROM group_members WHERE group_id = ".(int)$_GET['groupname']." ORDER BY light_order";
	$Result 	= mysqli_query($DB, $Query);
	$_cfg["ShowGroup"]	= 1;
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{ 
		PrintArrayBasic("", $row["light_id"]);
	}	
}
else
{
	$_cfg["ShowGroup"]	= 0;
	$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
	$Config		= GetJSON($Url);
	array_walk($Config,"PrintArrayBasic");
}
?>
  </tbody>
</table>