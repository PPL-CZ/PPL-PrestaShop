function adminOrder (orderId) {
    const id = `#ppl-order-panel-shipment-div-${orderId}-overlay`

    const disable = () => {
        jQuery(`${id} button`).prop('disabled', true);
    }
    const enable = () => {
        jQuery(`${id} button`).prop('disabled', false);
    }

    const get_data = (element) => {
        const orderId = jQuery(element).data("orderid");
        const shipmentId = jQuery(element).data("shipmentid");
        const packageId =  jQuery(element).data("packageid");
        const shipment = jQuery(element).data("shipment");
        const optionals = jQuery(element).data("optionals");
        const value = jQuery(element).data("value");
        return {
            orderId,
            shipmentId,
            packageId,
            shipment,
            optionals,
            value
        }
    }

    const makeUrl = (name, pathToReplace, values) => {
        return window.pplPlugin.makeUrl(name, pathToReplace, values);
    }

    const refresh = () => {
        return fetch(makeUrl("order", `/${orderId}/render`)).then(x => x.text()).then((res) => {
            jQuery(id).replaceWith(res);
        });
    }

    const run = (url, method) => {
        method = method || 'GET';
        return fetch(url, {
            method: method,
        }).then(x => {
            if (x.status >= 300)
                throw new Error(x.statusText);

        }).then(refresh).then(x => x.text()).then(x => {
            jQuery(id).replaceClass(orderId);
        }).catch(() => {
            enable();
        });
    }

    jQuery(id).each(function () {
        jQuery(this).find(".cancel-package").on("click", function (e) {
            e.preventDefault();
            disable();
            const data = get_data(this);
            run(makeUrl("shipment", `/${data.shipmentId}/cancel/${data.packageId}`), "DELETE");
        })

        jQuery(this).find(".ppl_available_print_setting").on("click", function (e)
        {
            e.preventDefault();
            disable();
            const data = get_data(this);
            window.pplPlugin = window.pplPlugin || [];

            const optionals = data.optionals;
            const value = data.value;
            const item = jQuery("<div>").prependTo("body")[0];
            let myrender = null;
            let umount = null;

            const render = (newValue) => {
                const values = {
                    optionals,
                    value: newValue,
                    onChange: function (value) {
                        const url = makeUrl("setting", "print");

                        const json = JSON.stringify({
                            value,
                            shipmentId: data.shipmentId
                        });

                        fetch(url, {
                            method: "POST",
                            body: json,
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        })
                        render(value);
                    },
                    "returnFunc": function (data) {
                        unmount = data.unmount;
                        myrender = data.render
                    },
                    "onFinish": function () {
                        refresh().then(() => {
                            unmount();
                        });
                    }
                }
                if (myrender) {
                    myrender(values)
                } else {
                    pplPlugin.push(["selectLabelPrint", item, values]);
                }
            }
            render(value);
        })


        jQuery(this).find(".add-package").on("click", function(e){
            e.preventDefault();
            disable();
            const data = get_data(this);
            (async () => {
                if (!data.shipmentId)
                    data.shipmentId = await fetch(makeUrl("order", `/${data.orderId}/shipment`), {
                        method: "POST",
                    }).then(x => {
                        return x.headers.get('x-entity-id');
                    });
                run(makeUrl("shipment", `/${data.shipmentId}/addPackage`), "PUT")
            })();
        });

        jQuery(this).find(".remove-package").on("click", function(e){
            e.preventDefault();
            disable();
            const data = get_data(this);
            run(makeUrl("shipment", `/${data.shipmentId}/removePackage`), "PUT");
        });

        const renderShipment = (shipment) => {
            // @ts-ignore
            const pplPlugin = window.pplPlugin = window.pplPlugin || [];
            //pplPlugin.push(["wpUpdateStyle", `ppl-order-panel-shipment-div-${orderId}-overlay`]);
            let unmount = null;
            const item = jQuery("<div>").prependTo("body")[0];
            pplPlugin.push(["newShipment", item, {
                "shipment": shipment,
                "returnFunc": function(data) {
                    unmount = data.unmount;
                },
                "onFinish": function() {
                    refresh().then(() => {
                        unmount();
                    });
                }
            }]);
        }

        jQuery(this).find(".create-labels").on("click", function (e) {
            disable();
            const data = get_data(this);
            const pplPlugin = window.pplPlugin = window.pplPlugin || [];
            let unmount = null;
            const item = jQuery("<div>").prependTo("body")[0];

            pplPlugin.push(["newLabel", item, {
                "hideOrderAnchor": false,
                "shipment": data.shipment,
                "returnFunc": function(data) {
                    unmount = data.unmount;
                },
                "onFinish": function(){
                    refresh().then(() => {
                        unmount();
                    })
                }
            }]);
        });

        jQuery(this).find(".create-labels-add").on("click", function (e) {
            disable();
            const data = get_data(this);

            const pplPlugin = window.pplPlugin = window.pplPlugin || [];
            let unmount = null;
            const item = jQuery("<div>").prependTo("body")[0];

            pplPlugin.push(["selectBatch", item, {
                "hideOrderAnchor": false,
                "items": { items: [{ shipmentId: data.shipmentId, orderId: data.orderId }]},
                "returnFunc": function(data) {
                    unmount = data.unmount;
                },
                "onClose": function(){
                    refresh().then(() => {
                        unmount();
                    })
                }
            }]);
        });

        jQuery(this).find(".refresh-shipments-labels").on("click", function (e) {
           disable();
           const data = get_data(this);
           run(makeUrl("shipment", `/${data.shipmentId}/refreshLabels`),"PUT");
        });

        jQuery(this).find(".refresh-shipments-states").on("click", function (e) {
            disable();
            const data = get_data(this);
            run(makeUrl("shipment", `/${data.shipmentId}/refreshStates`),"PUT");
        });


        setTimeout(function() {
            var refresh = jQuery(`${id} .refresh-shipments-labels`);
            if(refresh.length)
            {
                refresh.prop("disabled", false).click();
            }
        }, 5000);


        jQuery(this).find(".remove-shipment").on("click", function (e) {
            disable();
            const data = get_data(this);
            run(makeUrl("shipment", `/${data.shipmentId}`), "DELETE");
        });

        jQuery(this).find(".detail-shipment").on("click", function(e)
        {
            e.preventDefault();
            disable();
            const data = get_data(this);
            if (data.shipmentId)
            {
                renderShipment(data.shipment);
            } else {
                const url = makeUrl("order", `/${orderId}/shipment`);
                fetch(url, { method: "POST" }).then(x => {
                    if (x.status === 201)
                    {
                        const id = x.headers.get("x-entity-id");
                        fetch(makeUrl("shipment", `/${id}`)).then(x => x.json()).then(x=> {
                            refresh().then(y => {
                                disable();
                                renderShipment(x);
                            });
                        });
                    } else {
                        enable();
                    }
                })

            }
        })
    });
}