<h1>Groepen</h1>

<style type="text/css">
#sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
#sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; border:#000 solid 1px; cursor:all-scroll; background-color: #FFF}
#sortable li span { margin-left: -1.3em; }
</style>
<script>

$(function() {
    $( "#sortable" ).sortable({
      connectWith: ".connectedSortable",
        update : function () 
		{
		var order = $('#sortable').sortable('toArray').toString();
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeOrder&GroupOrder='+order,true);
		xmlhttp.send();
		}
    }).disableSelection();
  });
  
  
function ChangeGroupname(group_id)
{
	var group = $('#groupname_'+group_id).html();
	var groupname=prompt("Enter a new group name",group);
	if (groupname!=null)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=RenameGroup&GroupID='+group_id+'&GroupName='+groupname,true);
		xmlhttp.send();
		$('#groupname_'+group_id).html(groupname);
	}
}

function DeleteGroup(group_id)
{
	var output=confirm("Groep wissen?");
	if (output==true)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=DeleteGroup&GroupID='+group_id,true);
		xmlhttp.send();
    $('#'+group_id).hide('slow', function()  { });
	}
}

</script>
<p>Je kan hier de volgorde van de groepen aanpassen en de inhoud van de groep bewerken.</p>

<table>
  <thead>
    <tr>
      <th>Groep</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
		
        <ul id="sortable" class="connectedSortable">
<?php

	//opgegeven groep ophalen
	$Query    	= "SELECT group_id, group_name, group_order, timestamp FROM groups ORDER BY group_order";
	$Result 	= mysqli_query($DB, $Query);
	$i				= 0;
	while ($row = mysqli_fetch_array($Result, MYSQLI_ASSOC))
	{ 
		if ($_cfg["ModRewrite"]) 
		{ 
			$Link = "groups/".$row["group_id"];
		}
		else
		{
			$Link = "index.php?Page=group_detail&group_id=".$row["group_id"];
		}
		echo "        <li id=\"".$row["group_id"]."\">
				<span style=\"float:left\" id=\"groupname_".$row["group_id"]."\">".$row["group_name"]."</span>
				<span style=\"float:right\">
					<a href=\"".$Link."\"><img src=\"images/lamp.png\" /></a>&nbsp;
					<img src=\"images/edit.png\" onclick=\"ChangeGroupname(".(int)$row["group_id"].");\" style=\"cursor:pointer\" />&nbsp;
					<img src=\"images/del.png\" style=\"cursor:pointer\" onclick=\"DeleteGroup(".(int)$row["group_id"].");\"  />
				</span></li>\n";
		
		//groupering weer bijwerken. deze kan gaten bevatten ivm wissen van groepen
		mysqli_query($DB, "UPDATE groups SET group_order = '".$i."' WHERE group_id = ".(int)$row["group_id"])or die('Unable to execute query. '. mysqli_error($DB));
		$i++;
	}
?>
</ul>
</td>
    </tr>
  </tbody>
</table>