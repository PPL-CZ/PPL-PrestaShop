<div class="btn-group">
    <button class="btn btn-outline-secondary dropdown-toggle js-bulk-actions-btn ppl_filter_toggle" data-toggle="dropdown">
                    <span class="ppl_filter_toggle_text">
                    {if ($ppl_filter_state)}
                        Pouze s PPL dopravou
                    {else}
                        Bez filtrace PPL
                    {/if}
                    </span>
        <i class="icon-caret-up"></i>
    </button>
    <div class="dropdown-menu">
        <button id="order_grid_bulk_action_change_order_status" class="dropdown-item js-bulk-action-btn ppl_filter_on" type="button" data-route="admin_orders_view" data-route-param-name="orderId" >
            Pouze s PPL dopravou
        </button>
        <button id="order_grid_bulk_action_ppl_order_print" class="dropdown-item js-bulk-action-btn ppl_filter_off" type="button" data-route="admin_orders_view" data-route-param-name="orderId" >
            Bez filtrace PPL
        </button>
    </div>
</div>
<script type="text/javascript">
    (function() {
        function vyhledani_click () {
            var v = jQuery("[name=\"order[actions][search]\"]");
            var disabled = v.prop("disabled");
            v.prop("disabled", false);
            v.click();
            v.prop("disabled", disabled);
        }

        function filter() {
            return jQuery("input[name=\"order[ppl_filter]\"");
        }

        var text = jQuery(".ppl_filter_toggle_text");
        jQuery(document).on("click", "button.ppl_filter_on", function() {
            text.text(this.innerText);
            filter().val("1")
            vyhledani_click();
        });
        jQuery(document).on("click", "button.ppl_filter_off",function() {

            jQuery(".ppl_filter_toggle_text").text(this.innerText);
            text.val("")
            filter().val("-1");
            vyhledani_click();
        });
    })();
</script>
