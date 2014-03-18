<h1>Gebruiker aanmaken</h1>
<p>De gebruiker '<strong><?= $_cfg["BridgeHash"] ?></strong>' bestaat niet op de Bridge.</p>
<p>Pas eventueel de 'Hash' aan in <strong>/includes/settings.php</strong>.</p>
<p>Druk eerst op de connect knop van de Hue bridge, en daarna op de POST knop hier onder om de gebruiker aan te maken op de Hue Bridge.</p>
        <style>
        
            #buttons{
                clear: left;
            }
            input{
                width: 100%;
            }
            form{
                background: #e8e8e8;
                width: 800px;
                padding: 20px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
            }
            textarea{
                padding-top: 10px;
                width: 100%;
                font-family: monaco,monospace;
                font-size: 12px;
                -webkit-border-radius: 10px;
                -moz-border-radius: 10px;
            }
        </style>
       <form name="commandform" >
            <input name="commandurl" type="text" size="60" value="http://<?= $_cfg["BridgeIP"] ."/api"?>">
            <div id="buttons">
                <button type="button" onclick="getHTML('POST')">POST</button>
            </div>
            <h2>Message Body:</h2>
            <textarea name="messagebody" rows="5" cols="100">{"devicetype":"Php script","username":"<?= $_cfg["BridgeHash"] ?>"}</textarea>
            <h2>Command Response:</h2>
            <textarea name="response" rows="20" cols="100"></textarea>
</form>
        
<script language="JavaScript">
function getHTML(command)
{
    if (window.XMLHttpRequest)
    {
        var http = new XMLHttpRequest();
        http.open(command, document.commandform.commandurl.value, true);

        http.onreadystatechange = function()
        {
            if(http.readyState == 4)
            {
                if(http.status==200)
                {
                    document.commandform.response.value="Bad JSON: "+http.responseText
                    document.commandform.response.value=JSON.stringify(JSON.parse(http.responseText), null, '\t');
                }
                else
                {
                    document.commandform.response.value="Error "+http.status
                }
            }
        }
        http.send(document.commandform.messagebody.value);
    }
    return false;
}
</script>