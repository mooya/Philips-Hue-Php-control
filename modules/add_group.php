<h1>Groep aanmaken</h1>
<?php
function PrintArrayBasic($item, $key)
{
	global $_cfg;
	if (is_array($item))
	{
		if (!in_array($key,$_cfg["HideLights"] ))
		{
			//Key is het lamp nummer. Extra info ophalen
			$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/".$key;
			$Light			= GetJSON($Url);
	
			$Img			= "";
			if ($Light["state"]["reachable"] == false)
			{ 
				$Img = "<img src=\"images/alert.png\">"; 
			}
	
			 $StatusImg		= "<img src=\"images/button-off.png\" style=\"cursor:pointer\" width=\"74\" height=\"25\" id=\"Light_".$key."_img\" onclick=\"ToggleLight(".$key.")\">";
			 if ($Light["state"]["on"] == 1)
			 {
				 $StatusImg		= "<img src=\"images/button-on.png\" style=\"cursor:pointer\" width=\"74\" height=\"25\" id=\"Light_".$key."_img\" onclick=\"ToggleLight(".$key.")\">";
			 }
			echo "    <tr>\n";
			echo "      <td style=\"text-align: left;\"><input type=\"checkbox\" name=\"lights[]\" value=\"".$key."\" /></td>\n";
			echo "      <td style=\"text-align: left;\">".$key."</td>\n";
			echo "      <td style=\"text-align: left;\">". $item["name"]." <i>".$Img."<br />".$Light["type"]."</i></td>\n";
			echo "      <td style=\"text-align: left;\">".$StatusImg."</td>\n";
			echo "    </tr>\n";
		}
	}
}


?>

<style type="text/css">
#sortable { list-style-type: none; margin: 0; padding: 0; width: 500px; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; border:#000 solid 1px; cursor:all-scroll; background-color: #FFF}
#sortable li span { position: absolute; margin-left: -1.3em; }
</style>
<script>

$(function() {
    $( "#sortable" ).sortable({
      connectWith: ".connectedSortable",
        update : function () 
		{
		var order = $('#sortable').sortable('toArray').toString();
		document.form.group_members.value = order;
		}
    }).disableSelection();
  });
  
</script>
<?php CreateLightGroups ($_GET['groupname']); 

echo "<p>Selecteer de lampen die je wilt gebruiken in de nieuwe groep.</p>\n";

	if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['group_name']) && isset($_POST['group_members']))
	{
		$Query		= "INSERT INTO groups (group_name, timestamp) VALUES ( '" .add($_POST['group_name'])."', '".time()."' );";
		$Result		= mysqli_query($DB, $Query);
		$GroupID	= mysqli_insert_id($DB);
		
		$GroupMembers= explode(",",$_POST['group_members']);
 		for($i = 0; $i  < count($GroupMembers); $i++)
 		{ 
			$Query		= "INSERT INTO group_members (group_id, light_id, light_order) VALUES ( ".(int)$GroupID.", ".(int)$GroupMembers[$i].", ".(int)$i." );";
			$Result 	= mysqli_query($DB, $Query);
		}
		
		echo "<script>\n";
		echo "$( document ).ready(function() {\n";
		echo "	var url =\"lights/".$GroupID."\";\n";
		echo "	$(location).attr('href',url);\n";
		echo "});";
		echo "</script>\n";
	}
	elseif ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['lights']))
	{
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
		$Config		= GetJSON($Url);
?>
<form name="form" method="post" action="">

<table>
  <tbody>
    <tr>
      <td>Titel</td>
      <td><input name="group_name" type="text" value="groepsnaam"></td>
    </tr>
    <tr>
      <td>Volgorde</td>
      <td><?php
		echo "<ul id=\"sortable\" class=\"connectedSortable\">\n";
		$group_members = "";
 		for($i = 0; $i  < count($_POST['lights']); $i++)
 		{ 
			$Value		= $_POST['lights'][$i];
			$Name		= $Config[$Value]["name"];
			echo "<li id=\"".$Value."\" ><span class=\"ui-icon ui-icon-arrowthick-2-n-s\"></span>".$Value." - ".$Name."</li>\n";
			$group_members .= $Value.","; 
		}
		echo "</ul>\n";

?><input name="group_members" id="group_members" type="hidden" value="<?= substr( $group_members,0,( strlen($group_members) -1));?>" ></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="submit"  value="Opslaan"  /></td>
    </tr>
  </tbody>
</table>

</form>


<?php

	}
	else
	{
		
?>
<form name="form" method="post" action="">
<input type="submit"  value="Volgende stap"  />
<table>
  <thead>
    <tr>
      <th>Selecteer</th>
      <th>Nummer</th>
      <th>Naam</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
<?php
$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
$Config		= GetJSON($Url);

array_walk($Config,"PrintArrayBasic");
?>
  </tbody>
</table>

</form>
<?php } ?>
