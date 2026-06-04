jQuery(document).on("click", "button[data-select-parcel-shop],a[data-select-parcel-shop]", function(ev) {
    ev.preventDefault();

    var self = this;
    const address = jQuery(this).data('address');
    const country = jQuery(this).data('country')
    const hiddenPoints = jQuery(this).data("hiddenpoints");
    const countries = jQuery(this).data("countries");


    const url = new URL(window.FrontMapPPLController);
    url.searchParams.set("ajax", "1");
    url.searchParams.set("action", "SetParcel")
    const setparcel = async (data)=> {
        await fetch(`${url}`, {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            }
        }).then(x => {
            if (x.status !== 200)
            {
                throw Error("not found");
            }
            return x;
        }).then(x =>  x.text()).then(x => {
            jQuery(self).closest(".pplcz-method-container").replaceWith(x);
        })
    }

    const adding = {

    }

    if (address)
        adding.address = address;
    if (country)
        adding.country = country;
    if (hiddenPoints)
        adding.hiddenPoints = hiddenPoints;
    if (countries)
        adding.countries = countries;

    switch(jQuery(this).data("select-parcel-shop"))
    {
        case "show":
            PplMap(function() {}, { lat: jQuery(this).data("lat"), lng: jQuery(this).data("lng"), ...adding });
            break;
        case "cash":
            PplMap(function(data) {
                setparcel(data)
            }, { withCash: true, ...adding });
            break;
        case "clear":
            setparcel(null)
            jQuery("body").trigger("update_checkout");
            break;
        default:
            PplMap(function(data) {
                setparcel(data);
            }, adding);
            break;
    }

});