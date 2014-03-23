<h1>Lampen in de groep</h1>

<style type="text/css">
#sortable1  { list-style-type: none; margin: 0; padding: 0; width: 100%; }
#sortable1 li  { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; border:#000 solid 1px; cursor:all-scroll; background-color: #FFF}
#sortable1 li span { margin-left: -1.3em; }

#sortable2  { list-style-type: none; margin: 0; padding: 0; width: 100%; }
#sortable2 li  { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; border:#000 solid 1px; cursor:all-scroll; background-color: #FFF}
#sortable2 li span { margin-left: -1.3em; }

</style>
<script>

$(function() {
    $( "#sortable1, #sortable2" ).sortable({
      connectWith: ".connectedSortable",
        update : function () 
		{
		var order = $('#sortable1').sortable('toArray').toString();
		//alert(order);
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeMembers&group_id=<?=$_GET['group_id'] ?>&LightOrder='+order,true);
		xmlhttp.send();
		}
    }).disableSelection();
  });


  
</script>
<p>Selecteer de lampen die je wilt gebruiken in de groep. Je kan ook de volgorde aanpassen.</p>
<table>
  <thead>
    <tr>
      <th width="50%">Inclusief</th>
      <th width="50%">Exclusief</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><ul id="sortable1" class="connectedSortable">
<?php
	//opgegeven groep ophalen
	$Query    	= "SELECT light_id, light_order FROM group_members WHERE group_id = ".(int)$_GET['group_id']." ORDER BY light_order";
	$Result 	= mysqli_query($DB, $Query);
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{ 
		$LightsArray[$row["light_id"]] = 1;
		$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights/". $row["light_id"];
		$Light			= GetJSON($Url);
		echo "	<li id=\"".$row["light_id"]."\">".$Light["name"]."</li>\n";
	}
?></ul></td>
      <td><ul id="sortable2" class="connectedSortable">
<?php

	$Url 				= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]."/lights";
	$Config		= GetJSON($Url);
	
	
	for ($i=1; $i<count($Config); $i++)
	{
		  if (!isset($LightsArray[$i]))
		  {
				echo "<li id=\"".$i."\">".$Config[$i]["name"]."</li>\n";
		  }
	}
	
?></ul></td>
    </tr>
  </tbody>
</table>
<input type="button" value="Terug" onclick="window.location='<?php if ($_cfg["ModRewrite"]) { echo $_cfg["WebsiteURL"]."lights/". $_GET['group_id']; } else{ echo $_cfg["WebsiteURL"] ."index.php?Page=lights&groupname=".$_GET['group_id']; } ?>'">