<?php

/*
Checkbox image opmaken
*/
function CheckoxImage($value)
	{
    $ckecked 	= "checkbox_checked.gif";
    $unckeched 	= "checkbox_unchecked.gif";
   
    if ( $value == 0 ) 
		{
       	return "<img src=\"images/".$unckeched."\" width=\"16\" height=\"16\" border=\"0\">";
    	}
		else 
		{
       	return "<img src=\"images/".$ckecked."\" width=\"16\" height=\"16\" border=\"0\">";
   	}
}

function GetJSON($Url)
{
	$cf 				= curl_init($Url);
	curl_setopt($cf, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cf, CURLOPT_CUSTOMREQUEST, "GET");
	$response 	= curl_exec($cf);
	$Output	 	= json_decode($response,true);
	return $Output;
}

function DeleteJSON($Url)
{
	$cf 				= curl_init($Url);
	curl_setopt($cf, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cf, CURLOPT_CUSTOMREQUEST, "DELETE");
	$response 	= curl_exec($cf);
	$Output	 	= json_decode($response,true);
	return $Output;
}


function PutJSON($Url, $Data)
{
	/*
		$Url 				= "http://".$_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$Light."/state";
		$Data			= "{\"on\":true}";
		PutJSON($Url,$Data);
	*/
	
	$ch = curl_init($Url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $Data);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	$response = curl_exec($ch);

	if(!$response)
	{
		return false;
	}
	
	return ( $response);
}

function sec2hms ($sec, $padHours = false) 
	{
	$hms = "";  
    $hours = intval(intval($sec) / 3600); 
    $hms .= ($padHours) 
          ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
          : $hours. ':';     
    $minutes = intval(($sec / 60) % 60); 
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
    $seconds = intval($sec % 60); 
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);
	return $hms;
}

function Sunrise($Time )
{
	global $_cfg;
	$zenith=90+50/60;
	$sunrise = date_sunrise($Time, SUNFUNCS_RET_STRING, $_cfg["Timezone"]["Latiture"], $_cfg["Timezone"]["Longitude"], $zenith,$_cfg["Timezone"]["Offzet"]);
	
	$sunrise	= strtotime(date("Y-m-d",$Time) ." ". $sunrise);
	if (date("I",$sunrise) == 1)//Met zomertijd een uur erbij doen.
	{
		$sunrise	= $sunrise + 3600;
	}
	return $sunrise;
}


function Sunset($Time)
{
	global $_cfg;
	$zenith=90+50/60;
	$sunset = date_sunset($Time, SUNFUNCS_RET_STRING, $_cfg["Timezone"]["Latiture"], $_cfg["Timezone"]["Longitude"], $zenith,$_cfg["Timezone"]["Offzet"]);
	
	$sunset	= strtotime(date("Y-m-d",$Time) ." ". $sunset);
	
	if (date("I",$sunset) == 1)//Met zomertijd een uur erbij doen.
	{
		$sunset	= $sunset + 3600;
	}
	
	return $sunset;
}


function add ($string)
{
	global $DB;
    return mysqli_real_escape_string($DB, $string);
}

function CheckValues ($value1, $value2, $return1, $return2)
	{
	if ($value1 == $value2)
		{
		return $return1;
		}
		else
		{
		return $return2;		
	}
}


function thumbgenerator ($image, $saveorshow, $quality, $thumbsize, $savename)
	{ 
	$ext		= strtolower(substr(strrchr($image, '.'), 1));
	$continue 	= true;
	if ($ext == "gif")
		{
		$handle = @imagecreatefromgif($image);
		}
		elseif ($ext == "jpg" || $ext == "jpeg")
		{
		$handle = @imagecreatefromjpeg($image);
		}
		elseif ($ext == "png")
		{
		$handle = @imagecreatefrompng($image);
		}
		else
		{
		$continue = false;
	}
	
	if ($continue)
		{		
		$x = imagesx($handle); // Image Original Width
		$y = imagesy($handle); // Image Original Height
	
		// Calculating whitch side is larger
		if ( $x > $y )
			{
			$max = $x;
			$min = $y;
		}
	
		if ( $x <= $y )
			{
			$max 	= $y;
			$min 	= $x;
		}
	
		$rate 		= $max / $thumbsize; // Thumbnail Ratio
		$final_x 	= $x / $rate;
		$final_y 	= $y / $rate;
	
		if( $final_x > $x ) 
			{
			$final_x = $x;
			$final_y = $y;
		}

		$final_x 	= ceil ( $final_x ); // Thubnail Width
		$final_y 	= ceil ( $final_y ); // Thubnail Height
		$black_picture = imagecreatetruecolor ( $final_x, $final_y ); // Generating blank image for thumbnail

		imagefill ( $black_picture, 0, 0, imagecolorallocate ( $black_picture , 255, 255, 255 ) );
		imagecopyresampled ( $black_picture, $handle, 0, 0, 0, 0, $final_x, $final_y, $x, $y );
		if ( $saveorshow == "show" ) { header ( "Content-type: image/jpeg" ); imagejpeg ( $black_picture, "", $quality); }
		if ( $saveorshow == "save" ) imagejpeg ( $black_picture, $savename, $quality);
		imagedestroy ( $handle );
		imagedestroy ( $black_picture );
	}
}

function CreateLightGroups ($groupname)
{
	global $_cfg, $DB;
	echo "        <div id=\"lights_menu\">\n";
	echo "      	      <ul class=\"navigatie\">\n";
 	$Query    	= "SELECT group_id, group_name, group_order FROM groups ORDER BY group_order";
	$Result 	= mysqli_query($DB, $Query);
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		echo "      	      <li><a  href=\"".$_cfg["WebsiteURL"] ."lights/".$row["group_id"]."\"".CheckValues($groupname ,$row["group_id"]," class=\"active\"","").">".$row["group_name"]."</a></li>\n";
	}
	echo "      	      <li><a  href=\"".$_cfg["WebsiteURL"] ."lights/all\"".CheckValues($groupname,"all"," class=\"active\"","").">Alles</a></li>\n";
	echo "      	      <li><a  href=\"".$_cfg["WebsiteURL"] ."lights/addgroup\"".CheckValues($groupname,"addgroup"," class=\"active\"","").">Groep toevoegen</a></li>\n";
	echo "      	      </ul>\n";
	echo "        </div>\n";
}

function CreateScenePages($page_id)
{
	global $_cfg, $DB;
	echo "        <div id=\"lights_menu\">\n";
	echo "      	      <ul class=\"navigatie\" id=\"PageNav\">\n";
 	$Query    	= "SELECT page_id, page_name, page_order FROM scene_pages ORDER BY page_order";
	$Result 	= mysqli_query($DB, $Query);
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{
		echo "      	      <li class=\"navigatie_item\" id=\"Page_".$row["page_id"]."\"><a  href=\"javascript:GotoPage(".$row["page_id"].")\"".CheckValues($page_id ,$row["page_id"]," class=\"active\"","")."><span  id=\"PageName_".$row["page_id"]."\">".$row["page_name"]."</span><span class=\"DelPage\"><img src=\"images/icon_edit_25x25.png\"  onclick=\"EditPage(".$row["page_id"].")\"> <img src=\"images/icon_delete_25x25.png\" onclick=\"DelPage(".$row["page_id"].")\" ></span></a></li>\n";
	}
	echo "      	      <li><a  href=\"javascript:AddPage()\">Pagina toevoegen</a></li>\n";
	echo "      	      </ul>\n";
	echo "        </div>\n";
}

function LoginCheck()
{
	//kijken of de user is ingelogd.
	if (isset($_SESSION['LoginCheck']) && $_SESSION['LoginCheck'] == 1 )
	{
		return true;
	}
	else
	{
		return false;			
	}	
}

?>