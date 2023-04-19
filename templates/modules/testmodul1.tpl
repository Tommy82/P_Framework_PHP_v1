{extends file='basics/frontend.tpl'}

{block name='content'}
    Mein neuer Content aus dem Testmodul 1
    <br>
    <table>
        <tbody>
            {foreach from=$ELEMENTS.element item=element}
                <tr>
                    <td>{$element["node1"]}</td>
                    <td>{$element["node2"]}</td>
                    <td><a href="testmodul2/{$element["id"]}">details</a></td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/block}