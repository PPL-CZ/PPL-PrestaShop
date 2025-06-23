<div class="pplcz-method-container">
    <div class="pplcz-parcelshop-inner">
        <div class="select-parcelshop">
            <a href="#" class="select-parcelshop" data-select-parcel-shop="" {if isset($hiddenPoints)}data-hiddenpoints="{$hiddenPoints}"{/if}  {if isset($deliveryAddress) and $deliveryAddress}data-address="{$deliveryAddress}"{/if} {if isset($country) && $country}data-country="{$country}"{/if} {if isset($countries) && $countries}data-countries="{$countries}"{/if}>
                <img src="{$image}">
            </a>
        </div>
        <div class="selected-parcelshop">
        {if isset($parcel) and $parcel}
            {$parcel->getType()} <a href="#" class="clear-map" data-select-parcel-shop="clear">[zrušit]</a>,
            {$parcel->getName()|cat:' '|cat:$parcel->getName2()|trim}, {$parcel->getStreet()}, {$parcel->getZip()}, {$parcel->getCity()}, {$parcel->getCountry()}
        {else}
            Kliknutím na ikonu vyberte místo
        {/if}
        </div>
    </div>
</div>