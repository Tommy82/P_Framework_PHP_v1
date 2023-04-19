{extends file='basics/frontend.tpl'}

{block name='content'}
    Mein neuer Content aus dem Testmodul 3
    <br>
    <table>
        <tbody>
            {foreach from=$ITEMS item=element}
                <tr>
                    <td>{$element["id"]}</td>
                    <td>{$element["name"]}</td>
                    <td>{$element["detail"]}</td>
                    <td><a href="/testmodul4/{$element["id"]}">Details</a></td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/block}