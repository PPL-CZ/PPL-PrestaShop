jQuery(document).on("click", ".ppl_print_labels", function (ev){

    var div = jQuery("<div>").appendTo("body");
    var shipments = [];

    jQuery("input[type=checkbox]").each(function (){
        if (this.checked && ((this.name || '').indexOf("order_orders_bulk") === 0 || (this.name || '').indexOf("orderBox") === 0)) {
            const orderId = this.value;
            if (orderId) {
                shipments = shipments.concat(pplShipments.filter(x => x.shipment.orderId == orderId));
            }
        }
    })

    window.pplPlugin = window.pplPlugin  || [];
    const items = shipments.map(x => ({shipmentId: x.shipment.id, orderId: x.shipment.orderId}));
    window.pplPlugin.push(["selectBatch", div[0], {
        "hideOrderAnchor": false,
        "items": { items },
        "returnFunc": function(data) {
            unmount = data.unmount;
        },
        "onClose": function(){
            unmount();
            div.remove();
        }
    }]);
    /*

    pplPlugin.push(["newLabels", div[0], {
        shipments,
        "returnFunc": function(data) {
            unmount = data.unmount;
        },
        onFinish:function (){
            unmount();
        },
        onRefresh: function(orderIds) {
           /*
            wp.ajax.post({
                action: "ppl_orders_table",
                orderIds: orderIds
            }).done(function(item) {

                Object.keys(item.orders).forEach(function (key) {
                    jQuery("#ppl-order-panel-shipment-div-" + key + "-overlay").replaceWith(item.orders[key]);
                })
            })


        }
    }])
*/
    ev.preventDefault();
});