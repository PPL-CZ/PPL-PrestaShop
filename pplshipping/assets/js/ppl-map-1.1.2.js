function PplMap (onComplete, data) {


    const { withCard, withCash, lat, lng, address, country, hiddenPoints, countries } = data || {};

    var pplMap = document.createElement("div");
    pplMap.id = "ppl-parcelshop-map-overlay"


    var url = new URL(FrontMapPPLController);


    if (parseFloat(lat + "") && parseFloat(lng + "")) {
        url.searchParams.set("ppl_lat", lat);
        url.searchParams.set("ppl_lng", lng);
    }

    if (address)
    {
        url.searchParams.set("ppl_address", address)
    }

    if (withCard) {
        url.searchParams.set("ppl_withCard", "1");
    }
    if (withCash)
    {
        url.searchParams.set("ppl_withCash", "1");
    }

    if (country) {
        url.searchParams.set('ppl_country', country);
    }

    if (countries)
    {
        url.searchParams.set('ppl_countries', countries);
    }


    if (hiddenPoints)
        url.searchParams.set("ppl_hiddenpoints", hiddenPoints);



    var stringurl = '' + url;

    jQuery(pplMap).html('<div id="ppl-parcelshop-map-overlay2">' +
        '<a id="ppl-parcelshop-map-close" href="#" >Zavřít</a>' +
        '<iframe id="ppl-parcelshop-map" src="' + stringurl + '"></iframe>' +
        '</div>');

    jQuery(document.body).prepend(pplMap);

    function clear() {
        jQuery(pplMap).remove();
        window.removeEventListener("message", postEvent);
        jQuery("body").removeClass("ppl-parcelshop-hidden-overlay");
    }

    function postEvent (event) {
        const domain = url.protocol + "//" + url.host;
        if (event.origin && event.origin === domain) {
            try {
                const parsedData = JSON.parse(event.data);
                if (parsedData.parcelshop){

                    onComplete(parsedData.parcelshop);
                    clear();
                }
            }
            catch (e)
            {
                console.log(e);
            }

        }
    }

    window.addEventListener("message", postEvent);

    jQuery("body").addClass("ppl-parcelshop-hidden-overlay");

    jQuery("#ppl-parcelshop-map-close").on("click", function(ev) {
        ev.preventDefault();
        clear();
    })
}