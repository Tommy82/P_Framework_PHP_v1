{extends file='basics/frontend.tpl'}

{block name='content'}
    Mein neuer Content aus dem Testmodul 4
    <br>

    <div>Id: {$ITEM["id"]}</div>
    <div>Name: {$ITEM["name"]}</div>
    <div>Detail: {$ITEM["detail"]}</div>

    <div>AddOns:
        <table>
            {foreach from=$ADDONS item=element}
                <tr>
                    <td>{$element["id"]}</td>
                    <td>{$element["addOn"]}</td>
                </tr>
            {/foreach}
        </table>
    </div>

{/block}