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
	die();
}

if (isset($_GET['Action']))
{
	//Gebruiker op de bridge wissen
	if ($_GET['Action']  == "DeleteUser" && isset($_GET['user']) )
	{
		$return = DeleteJSON($_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/config/whitelist/".$_GET['user']);
		
		if (isset($return[0]["error"]["description"]))
		{
			echo "Fout: ".$return[0]["error"]["description"];
		}
		elseif (isset($return[0]["success"]))
		{
			echo str_replace("/config/whitelist/","",$return[0]["success"]);
		}
	}

	//Gekozen scene instellen
	if ($_GET['Action'] == "SetScene" && isset($_GET['scene']) )
	{
		$Query    	= "SELECT light_id, state, colormode, brightness, hue, saturation, color_temp FROM scene_settings WHERE scene_id = ".(int)$_GET['scene'];
		$Result 	= mysqli_query($DB, $Query);
		while ($Scene = mysqli_fetch_array($Result, MYSQLI_ASSOC))
		{
			if ($Scene["state"] <> "on")
			{
				//lamp uitzetten
				$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$Scene["light_id"]."/state";
				$Data			= "{\"on\":false}";
				PutJSON($Url,$Data);
			}
			else
			{
				if ($Scene["colormode"] == "ct")
				{
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$Scene["light_id"]."/state";
					$Data			= "{\"on\":true, \"ct\":".(int)$Scene["color_temp"].",\"bri\":".(int)$Scene["brightness"]."}";
					PutJSON($Url,$Data);
				}
				elseif ($Scene["colormode"] == "hs")
				{
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$Scene["light_id"]."/state";
					$Data			= "{\"on\":true, \"hue\":".(int)$Scene["hue"].", \"sat\":".(int)$Scene["saturation"].", \"bri\":".(int)$Scene["brightness"]."}";
					echo $Data."<br>";
					PutJSON($Url,$Data);
				}			
			}
		}
	}
	
	//Groep volgorde aanpassen
	if ($_GET['Action'] == "ChangeOrder" && isset($_GET['GroupOrder']) )
	{
		$Order	= explode(",",$_GET['GroupOrder']);
		for ($i=0; $i< count($Order); $i++)
		{
			$Query 		= "UPDATE groups SET group_order = '".$i."' WHERE group_id = ".(int)$Order[$i];
			$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		}
	}
	
	//Groep hernoemen
	if ($_GET['Action']  == "RenameGroup" && isset($_GET['GroupID']) && isset($_GET['GroupName']) )
	{
		$Query 		= "UPDATE groups SET group_name = '".add($_GET['GroupName'])."' WHERE group_id = ".(int)$_GET['GroupID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
	}
	
	//lampen volgorde in de groep aanpassen
	if ($_GET['Action']  == "ChangeMembers" && isset($_GET['LightOrder'])&& isset($_GET['group_id']) )
	{
		$Order	= explode(",",$_GET['LightOrder']);
		mysqli_query($DB, "DELETE FROM group_members WHERE group_id = ".(int) $_GET['group_id'] )or die('Unable to execute query. '. mysqli_error($DB));
		 
		 for ($i=0; $i< count($Order); $i++)
		 {
			$Query		= "INSERT INTO group_members (group_id, light_id, light_order) VALUES ( ".(int) $_GET['group_id'].", ".(int)$Order[$i].", ".(int)$i." );";
			$Result 	= mysqli_query($DB, $Query);
		}
	}
	
	//Groep wissen
	if ($_GET['Action']  == "DeleteGroup" && isset($_GET['GroupID']) )
	{
		$Query 		= "DELETE FROM groups WHERE group_id = ".(int)$_GET['GroupID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		$Query 		= "DELETE FROM  group_members WHERE group_id = ".(int)$_GET['GroupID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
	}
	
	//Lamp aanpassen adhv de coordinaten uit het 'drag' overzicht:
	if ($_GET['Action']  == "ChangeLightByCoor" && isset($_GET['light_id']) && isset($_GET['left']) && isset($_GET['top'])  )
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id'];
		$Light			= GetJSON($Url);
		
		
		if (!isset($Light["state"]["hue"]))
		{
			//echo "kleur van deze lamp kan niet aangepast worden";
		}
		else
		{
			if (isset($Light["state"]["ct"]))
			{
				//Dit is een Hue lamp, de TOP zit tussen 0 en  700
				if ($_GET['top'] < 230)
				{
					//temp waarde is tussen: 153/500
					//waarde left is van 0/700
					$Temp		= 153 + (($_GET['left'] / 700) * 347);
					//echo "kleur temperatuur aanpassen lamp naar:".$Temp;
	
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
					$Data			= "{\"on\":true,\"ct\":".round($Temp)."}";
					PutJSON($Url,$Data);
	
				}
				else
				{
					//Hue = 0 tm 65535
					//sat = 0 tm 255
					$Hue		= round(($_GET['left'] / 700) * 65535);
					$Sat			= round(($_GET['top']-230) / (700-230) * 255);
					//echo "kleur aanpassen hue lamp: Hue:".$Hue." Sat:".$Sat;
					
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
					$Data			= "{\"on\":true,\"hue\":".($Hue).",\"sat\":".($Sat)."}";
					PutJSON($Url,$Data);
				}
			}
			else
			{
				//Dit is een Living Colors lamp, de TOP zit tussen 0 en  450
				$Hue		= round(($_GET['left'] / 700) * 65535);
				$Sat			= round(($_GET['top'] / 450)  * 255);
				//echo "kleur aanpassen Living Colors  lamp: Hue:".$Hue." Sat:".$Sat;
				$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
				$Data			= "{\"on\":true,\"hue\":".($Hue).",\"sat\":".($Sat)."}";
				PutJSON($Url,$Data);
			}
		}
	}
	
	//Lamp aanpassen adhv de coordinaten uit het 'drag' overzicht, en return de settings vd lampen (voor creeren scene)
	if ($_GET['Action']  == "ChangeLightByCoorReturn" && isset($_GET['light_id']) && isset($_GET['left']) && isset($_GET['top'])  )
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id'];
		$Light			= GetJSON($Url);
		
		
		if (!isset($Light["state"]["hue"]))
		{
			//echo "kleur van deze lamp kan niet aangepast worden";
		}
		else
		{
			if (isset($Light["state"]["ct"]))
			{
				//Dit is een Hue lamp, de TOP zit tussen 0 en  700
				if ($_GET['top'] < 230)
				{
					//temp waarde is tussen: 153/500
					//waarde left is van 0/700
					$Temp		= 153 + (($_GET['left'] / 700) * 347);
					//echo "kleur temperatuur aanpassen lamp naar:".$Temp;
	
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
					$Data			= "{\"on\":true,\"ct\":".round($Temp)."}";
					PutJSON($Url,$Data);
					echo "<script>$('#light_'+".$_GET['light_id']."+'_colormode').val('ct');\n";
					echo "$('#light_'+".$_GET['light_id']."+'_color_temp').val('".round($Temp)."');</script>\n";
				}
				else
				{
					//Hue = 0 tm 65535
					//sat = 0 tm 255
					$Hue		= round(($_GET['left'] / 700) * 65535);
					$Sat			= round(($_GET['top']-230) / (700-230) * 255);
					//echo "kleur aanpassen hue lamp: Hue:".$Hue." Sat:".$Sat;
					
					$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
					$Data			= "{\"on\":true,\"hue\":".($Hue).",\"sat\":".($Sat)."}";
					PutJSON($Url,$Data);
					
					echo "<script>$('#light_'+".$_GET['light_id']."+'_colormode').val('hs');\n";
					echo "$('#light_'+".$_GET['light_id']."+'_hue').val('".round($Hue)."');\n";
					echo "$('#light_'+".$_GET['light_id']."+'_saturation').val('".round($Sat)."');</script>\n";
	
				}
			}
			else
			{
				//Dit is een Living Colors lamp, de TOP zit tussen 0 en  450
				$Hue		= round(($_GET['left'] / 700) * 65535);
				$Sat			= round(($_GET['top'] / 450)  * 255);
				//echo "kleur aanpassen Living Colors  lamp: Hue:".$Hue." Sat:".$Sat;
				$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".(int)$_GET['light_id']."/state";
				$Data			= "{\"on\":true,\"hue\":".($Hue).",\"sat\":".($Sat)."}";
				PutJSON($Url,$Data);
				echo "<script>$('#light_'+".$_GET['light_id']."+'_colormode').val('hs');\n";
				echo "$('#light_'+".$_GET['light_id']."+'_hue').val('".round($Hue)."');\n";
				echo "$('#light_'+".$_GET['light_id']."+'_saturation').val('".round($Sat)."');</script>\n";
			}
		}
	}
	
	//Scene volgorde aanpassen
	if ($_GET['Action'] == "ChangeSceneOrder" && isset($_GET['SceneOrder']) )
	{
		$Order	= explode(",",$_GET['SceneOrder']);
		for ($i=0; $i< count($Order); $i++)
		{
			$Query 		= "UPDATE scenes SET scene_order = '".$i."' WHERE scene_id = ".(int)$Order[$i];
			$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		}
	}

	//Pagina volgorde aanpassen van de scenes
	if ($_GET['Action'] == "ChangeScenePageOrder" && isset($_GET['PageOrder']) )
	{
		$Order	= explode(",",$_GET['PageOrder']);
		for ($i=0; $i< count($Order); $i++)
		{
			$Order[$i] = str_replace("Page_","",$Order[$i]);
			if (is_numeric($Order[$i]))
			{
				$Query 		= "UPDATE scene_pages SET page_order = '".$i."' WHERE page_id = ".(int)$Order[$i];
				$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
			}
		}
	}
	
	//Scene pagina aanmaken
	if ($_GET['Action'] == "AddScenePage" && isset($_GET['PageName']) )
	{
		$Query		= "INSERT INTO scene_pages (page_name,page_order ) VALUES ( '".add($_GET['PageName'])."', 9999 );";
		$Result 	= mysqli_query($DB, $Query) or die('Unable to execute query. '. mysqli_error($DB));
	}
	
	//Scene wissen
	if ($_GET['Action']  == "DelScene" && isset($_GET['SceneID']) )
	{
		$Query 		= "DELETE FROM scenes WHERE 	scene_id = ".(int)$_GET['SceneID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		$Query 		= "DELETE FROM  scene_settings WHERE scene_id = ".(int)$_GET['SceneID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
		//afbeelding staat er nog..
	}
	
	//Pagina hernoemen
	if ($_GET['Action']  == "RenamePage" && isset($_GET['PageID']) && isset($_GET['PageName']) )
	{
		$Query 		= "UPDATE scene_pages SET page_name = '".add($_GET['PageName'])."' WHERE page_id = ".(int)$_GET['PageID'];
		$Result 		= mysqli_query($DB, $Query)or die('Unable to execute query. '. mysqli_error($DB));
	}
	
	//Scene wissen
	if ($_GET['Action']  == "DelPage" && isset($_GET['PageID']) )
	{
		$Query    	= "SELECT scene_id, scene_order FROM scenes WHERE page_id = ".(int)$_GET['PageID']." ORDER BY scene_order";
        $Result 	= mysqli_query($DB, $Query);
        while ($Scene = mysqli_fetch_array($Result, MYSQLI_ASSOC))
        { 
			mysqli_query($DB, "DELETE FROM  scene_settings WHERE scene_id = ".(int)$Scene['scene_id'])or die('Unable to execute query. '. mysqli_error($DB));
		}
		
		mysqli_query($DB, "DELETE FROM scenes WHERE 	page_id = ".(int)$_GET['PageID'])or die('Unable to execute query. '. mysqli_error($DB));
		mysqli_query($DB, "DELETE FROM scene_pages WHERE 	page_id = ".(int)$_GET['PageID'])or die('Unable to execute query. '. mysqli_error($DB));
		//afbeelding staat er nog..
	}
	
	
	
}

?>