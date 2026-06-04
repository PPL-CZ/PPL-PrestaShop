<html>
<header>
    <title>Mapa</title>
    <link rel="stylesheet" href="{$map_css}" />
</header>
<body style="margin:0;padding:0">
<div id="pplcz-parcelshop-info"></div>
<ppl-access-point-widget
    id="pplWidget"
    api-key="{$apikey|escape:'htmlall':'UTF-8'}"
    config="{$config_json|escape:'htmlall':'UTF-8'}"
></ppl-access-point-widget>
<script type="text/javascript" src="{$map_js}"></script>
<script type="text/javascript" src="https://www.ppl.cz/accesspointwidget/loader.js"></script>
</body>
</html>
