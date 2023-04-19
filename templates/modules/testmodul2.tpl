{extends file='basics/frontend.tpl'}

{block name='content'}
    Mein neuer Content aus dem Testmodul 2
    <br>
    <table>
        <tbody>

            <div>ID: {$CURRENT["id"]}</div>
            <div>Node1: {$CURRENT["node1"]}</div>
            <div>Node2: {$CURRENT["node2"]}</div>
            <div>Node3:<br>

            {foreach from=$CURRENT.node3 item=element}
                <span>- {$element}</span><br>
            {/foreach}
            </div>
        </tbody>
    </table>
{/block}