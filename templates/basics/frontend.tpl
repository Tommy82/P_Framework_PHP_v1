<html lang="de">
    <head>
        <title>{$TITLE}</title>
    </head>
    <body>
        {block name='navigation'}
            <nav id="nav_main">
                <ul>
                    <li><a href="/">Startseite</a></li>
                    <li><a href="/testmodul1">Testmodul 1 (XML Daten)</a></li>
                    <li><a href="/testmodul3">Testmodul 3 (DB Daten)</a></li>
                </ul>
            </nav>
        {/block}
        {block name='main'}
            {block name='content'}{/block}
        {/block}
    </body>
</html>