                <h1>Debug</h1>
        <style>
        
            #buttons{
                clear: left;
            }
            input{
                width: 100%;
            }
            form{
                background: #e8e8e8;
                width:900px;
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
       <form name="commandform">
            <h1>CLIP API Debugger</h1>
            <h2>URL:</h2>
            <input name="commandurl" type="text" size="60" value="http://<?= $_cfg["BridgeIP"] ."/api/".$_cfg["BridgeHash"]?>">
            <div id="buttons">
                <button type="button" onclick="getHTML('GET')">GET</button>
                <button type="button" onclick="getHTML('PUT')">PUT</button>
                <button type="button" onclick="getHTML('POST')">POST</button>
                <button type="button" onclick="getHTML('DELETE')">DELETE</button>
            </div>
            <h2>Message Body:</h2>
            <textarea name="messagebody" rows="10" cols="100"></textarea>
            <h2>Command Response:</h2>
            <textarea name="response" rows="25" cols="100"></textarea>
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