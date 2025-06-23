<html>
    <header>
        <title>Mapa</title>
        <link
                rel="stylesheet"
                href="{$map_css}"
        />
        <link
                rel="stylesheet"
                href="https://www.ppl.cz/sources/map/main.css"
        />
        <script type="text/javascript" src="{$map_js}"></script>


    </header>
    <body style="margin:0;padding:0">
    <div id="ppl-parcelshop-info">PPL mapa</div>
    <div id="ppl-parcelshop-map" {foreach from=$maps key=k item=v} {$k}="{$v}" {/foreach}>
    </div>
    <script type="text/javascript" src="https://www.ppl.cz/sources/map/main.js"></script>
    </body>
</html>