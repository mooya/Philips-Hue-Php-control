<span style="float:left">
	<h1>Scenes </h1>
</span>

<?php if ( $_cfg["UseMysql"] 	) {?>
<span style="float:right">
	<img src="images/icon_edit_25x25.png" title="Pagina aanpassen" onclick="ChangePage()" />
	<img src="images/icon_new_25x25.png" title="Nieuwe scene maken" onclick="AddScene()" />
</span>
<?php } 

else { 
	echo "<br style=\"clear:both\"><p>Deze pagina werkt alleen indien MySQL is geactiveerd in de settings.php</p>";	
}?>

<br style="clear:both" />
<p id="Notes" style="display:none">Tijdens de Edit Modus kan je de pagina's sorteren door deze te slepen in de nieuwe volgorde. Je kan ook de Scenes in een andere volgorde slepen.</p>

<script language="javascript">
function SetScene(scene)
{
	var EditMode = $('#EditMode').val();
	if (EditMode == 0)//Nog niet in EditMode, hier wel in zetten.
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=SetScene&scene='+scene,true);
		xmlhttp.send();
	}
}

function GotoPage(PageID)
{
	var EditMode = $('#EditMode').val();
	if (EditMode == 0)//Nog niet in EditMode, hier wel in zetten.
	{
		<?php if ($_cfg["ModRewrite"]) { echo "window.location=('home/'+PageID);"; } else { echo "window.location=('?index.php?Page=home&page_id='+PageID);";  } ?>
	}
}

function AddScene()
{
	var PageID = $('#PageID').val();
	window.location=('add_scene?PageID='+PageID);
}

function EditScene(SceneID)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open('GET', 'AjaxHandler.php?Action=SetScene&scene='+SceneID,true);
	xmlhttp.send();
	xmlhttp.onreadystatechange=function() 
	{
		 if (xmlhttp.readyState==4) 
		 {
			window.location=('add_scene?SceneID='+SceneID);
		 }
	}
}

function DelScene(scene_id)
{
	var output=confirm("Scene wissen?");
	if (output==true)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=DelScene&SceneID='+scene_id,true);
		xmlhttp.send();
    	$('#'+scene_id).hide('slow', function()  { });
	}
}

function DelPage(page_id)
{
	var output=confirm("Pagina wissen?\n\n- Alle scenes op de pagina worden ook gewist.\n- Er wordt automatsich weer een lege pagina gemaakt indien er geen pagina's meer zijn.");
	if (output==true)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=DelPage&PageID='+page_id,true);
		xmlhttp.send();
		//als we op de te wissen pagina zitten, terug gaan naar Home
   		if ( $('#PageID').val() == page_id)
		{
			$('#Page_'+page_id).hide('slow', function()  { });
			$('#SceneContainer').hide('slow', function()  { window.location=('home'); });
		}
		else
		{
			$('#Page_'+page_id).hide('slow', function()  { });
		}
	}
}

function EditPage(page_id)
{
	var page_name = $('#PageName_'+page_id).html();
	var page_name=prompt("Enter a new group name",page_name);
	if (page_name!=null)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=RenamePage&PageID='+page_id+'&PageName='+page_name,true);
		xmlhttp.send();
		$('#PageName_'+page_id).html(page_name);
	}
}

function AddPage()
{
	var page_name=prompt("Enter a Page name",'');
	if (page_name!=null)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.open('GET', 'AjaxHandler.php?Action=AddScenePage&PageName='+page_name,true);
		xmlhttp.onreadystatechange=function() 
		{
			 if (xmlhttp.readyState==4) 
			 {
				location.reload();
			 }
		}
		xmlhttp.send();
	}
}

function ChangePage()
{
	$( ".DelButton" ).toggle( "slow", function()   { });
	$( ".DelPage" ).toggle( "slow", function()   { });
	$( "#Notes" ).toggle( "slow", function()   { });

	
	var EditMode = $('#EditMode').val();
	//var EditModeTimes = parseInt($('#EditModeTimes').val() );
	if (EditMode == 0)//Nog niet in EditMode, hier wel in zetten.
	{
		$('#EditMode').val(1);
		$(function() 
		{
			$( "#SceneContainer" ).sortable({
				update : function () 
				{
				var order = $('#SceneContainer').sortable('toArray').toString();
				xmlhttp = new XMLHttpRequest();
				xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeSceneOrder&SceneOrder='+order,true);
				xmlhttp.send();
				}});
			$( "#SceneContainer" ).disableSelection();
			
			
			$( "#PageNav" ).sortable({
				update : function () 
				{
				var order = $('#PageNav').sortable('toArray').toString();
				xmlhttp = new XMLHttpRequest();
				xmlhttp.open('GET', 'AjaxHandler.php?Action=ChangeScenePageOrder&PageOrder='+order,true);
				xmlhttp.send();
				}});
			$( "#PageNav" ).disableSelection();
			
			$("#SceneContainer").sortable("enable");
			$("#PageNav").sortable("enable");
			
			$('.Scene').css( 'cursor', 'all-scroll' );
			$('.navigatie_item a').css( 'cursor', 'all-scroll' );	
	});
	}
	else
	{
		$('#EditMode').val(0);
	//	$('#EditModeTimes').val(0);
		$("#SceneContainer").sortable("disable");
		$("#PageNav").sortable("disable");
		$('.Scene').css( 'cursor', 'pointer' );
		$('.navigatie_item a').css( 'cursor', 'pointer' );
	}  
}


</script>
<style type="text/css">
#SceneContainer
{
	width: 1000px;
	margin: 0 auto;
	overflow: auto;
}

#SceneContainerMain
{
	width: 1000px;
}

.Scene
{
	width: 200px;
	float:left;
    border: 1px solid #CBCBCB;
    box-shadow: 1px 1px 1px #CCCCCC;
	margin: 10px 10px 10px 10px;
    min-height: 110px;
    padding: 5px;
	cursor:pointer;
	text-align:center;
}


.Scene_Image
{
    border: 1px solid #CBCBCB;
    box-shadow: 1px 1px 1px #CCCCCC;
    min-height: 100px;
    margin-bottom: 5px;
}

.DelButton {
	padding-top: 11px;
	background-color:#FFF;
	border-bottom-left-radius: 5px;
	border-top-left-radius: 5px;
	border-bottom-right-radius: 5px;
	border-top-right-radius: 5px;
	border:#000 solid 1px;
	display:none;
	cursor:pointer;

}

.DelPage {
	padding-top: 11px;
	padding-left:5px; 
	padding-right:0px; 
	display:none;
	cursor:pointer;
	width:25px;
}
</style>
<?php
if ( $_cfg["UseMysql"] 	)
{
	if (!isset($_GET['page_id']) )
	{
		$Result 	= mysqli_query($DB, "SELECT page_id FROM scene_pages ORDER BY page_order");
		
		if (mysqli_num_rows($Result) == 0) 
		{			
			//Er bestaat nog geen pagina. 1tje aanmaken
			$Query		= "INSERT INTO scene_pages (page_name) VALUES ( 'Pagina 1' );";
			$Result		= mysqli_query($DB, $Query);
			$_GET['page_id'] 	= mysqli_insert_id($DB);
		}
		else
		{
			$Pages	 = mysqli_fetch_assoc($Result);
			$_GET['page_id'] = $Pages['page_id'];
		}
	}
	//Groepen ophalen uit de DB
	CreateScenePages ($_GET['page_id']);
}

?>
<div id="SceneContainerMain">

    <div id="SceneContainer">
        <?php
        if ( $_cfg["UseMysql"] )
        {
            $Query    	= "SELECT scene_id, scene_name,  scene_image, scene_order FROM scenes WHERE page_id = ".(int)$_GET['page_id']." ORDER BY scene_order";
            $Result 	= mysqli_query($DB, $Query);
            while ($Scene = mysqli_fetch_array($Result, MYSQLI_ASSOC))
            { 
               echo "<div class=\"Scene\" onclick=\"SetScene(".$Scene["scene_id"].")\" id=\"".$Scene["scene_id"]."\">\n";
               echo "<div class=\"Scene_Image\" style=\"background-image:url(images/scenes/".str_replace("original","big",$Scene["scene_image"]).")\" align=\"right\"><span class=\"DelButton\"><img src=\"images/icon_edit_25x25.png\"  onclick=\"EditScene(".$Scene["scene_id"].")\"> <img src=\"images/icon_delete_25x25.png\" onclick=\"DelScene(".$Scene["scene_id"].")\"></span></div>\n";
               echo $Scene["scene_name"]."\n";
               echo "</div>\n";
            }			
        }
        ?>
    </div>
</div>

<input name="EditMode" id="EditMode" value="0" type="hidden" />
<input name="PageID" id="PageID" value="<?= $_GET['page_id']?>" type="hidden" />