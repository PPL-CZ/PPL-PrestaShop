
<div id="pplshippingconfigure" class="{if isset($pplnewpresta)}pplnewpresta{/if} {if isset($ppl90presta)}ppl90presta{/if}">
    <div id="pplshippingcontent">
    </div>
</div>
<script type="text/javascript">
    window.pplPlugin = window.pplPlugin || [];
    jQuery("#content").removeClass("nobootstrap");
    window.pplPlugin.push(["optionsPage", "pplshippingcontent"])
</script>
<style>

    @media screen and (min-width: 1040px){

        #pplshippingconfigure:not(.ppl90presta) {
            position: relative;
            top: 120px;
        }

        #pplshippingconfigure.pplnewpresta {
            width: calc(100% - 230px);
            margin-left: calc(230px);
        }

        .page-sidebar-closed #pplshippingconfigure
        {
            margin-left: calc(0px) ;
        }
    }
    @media screen and (max-width: 1040px) {
        #pplshippingconfigure:not(.ppl90presta) {
            position: relative;
            top: 120px;
        }
    }
</style>