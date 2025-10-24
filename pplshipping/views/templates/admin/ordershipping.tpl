<div id="ppl-order-panel-shipment-div-{$orderId}-overlay" class="ppl-order-panel-shipment">
    {if isset($shipments) && $shipments}
        {foreach from=$shipments item=shipment key=key}
            {assign var="addId" value="`$orderId`~`$smarty.now`"}
            {assign var="recipient" value=$shipment->getRecipient()}
            {if $recipient}
                {assign var="recipient" value=([$recipient->getName(), $recipient->getContact(), $recipient->getStreet(), $recipient->getZip(), $recipient->getCity(), $recipient->getCountry(), $recipient->getPhone()]|pplfilter)}
                {assign var="recipient" value=($recipient|ppljoin:", ")}
            {/if}
            {assign var="packages" value=0}
            {if ($shipment->getPackages())}{assign var="packages" value=$shipment->getPackages()|count}{/if}
            <table>
                <tr>
                    <td style="text-align: right; padding-right: 1em">Služba:</td>
                    <td style="vertical-align: middle">
                        {if $shipment->getServiceCode()}
                            {assign var="imageurl" value=$shipment|pplimageurl}
                            {$shipment->getServiceName()} ({$shipment->getServiceCode()})
                            {if $imageurl}
                                <img src="{$imageurl}" height="20" style="position: relative; top: 5px"/>
                            {/if}
                        {else}
                            Nespecifikovaná služba
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; padding-right: 1em">Adresa:</td>
                    <td>
                        {if $recipient}
                            <div>
                                {$recipient}
                            </div>
                        {/if}
                        {if $shipment->getHasParcel() && $shipment->getParcel()}
                            {assign var="parcel" value=$shipment->getParcel()}
                            {assign var="parcelText" value=[$parcel->getName(), $parcel->getName2(), $parcel->getStreet(), $parcel->getCity(), $parcel->getCountry()]|pplfilter}
                            {assign var="parcelText" value=$parcelText|ppljoin:", "}
                            Zásilka(y) jsou určeny na {if $parcel->getType()|strtolower eq "parcelshop"}parcelshop{else}parcelbox{/if}:
                            <div>
                                {$parcelText}
                            </div>
                        {/if}
                    </td>
                </tr>
                {if $shipment->getNote()}
                    <tr>
                        <td style="text-align: right; padding-right: 1em">Poznámka:</td>
                        <td>{$shipment->getNote()}</td>
                    </tr>
                {/if}

                {if $shipment->getCodValue()}
                    <tr>
                        <td style="padding-right: 1em; text-align: right">
                            Dobírka:
                        </td>
                        <td>
                            {$shipment->getCodValue()} {if $shipment->isInitialized("codValueCurrency")}{$shipment->getCodValueCurrency()}{else}???{/if}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-right: 1em; text-align: right">Variabilní symbol:</td>
                        <td>{if $shipment->isInitialized("codVariableNumber")}{$shipment->getCodVariableNumber()}{else}Bez variabilního čísla{/if}</td>
                    </tr>
                {/if}
                {if !$shipment->getImportState() || $shipment->getImportState() eq 'None' || $shipment->getImportState() eq 'Error'}
                    <tr>
                        <td style="text-align: right; padding-right: 1em">
                            Počet zásilek:
                        </td>
                        <td>
                            {$shipment->getPackages()|count}
                            <button class="add-package btn btn-primary btn-sm "
                                    data-orderId="{$orderId}"
                                    {if $shipment->getId()}data-shipmentId="{$shipment->getId()}"{/if}

                            >
                                Přidat balík
                            </button>
                            {if $shipment->getPackages()|count > 1}
                                <button class="remove-package btn btn-sm btn-secondary "
                                        data-orderId="{$orderId}"
                                        {if $shipment->getId()}data-shipmentId="{$shipment->getId()}"{/if}>
                                    Odebrat balík
                                </button>
                            {/if}
                        </td>
                    </tr>
                {/if}
                {if $shipmentsErrors|pplisset:$key and $shipmentsErrors[$key] or $shipment->getImportErrors()}
                    <tr>
                        <td colspan="2">
                            <div class="alert alert-danger">
                                {foreach from=$shipmentsErrors[$key] item=item}
                                    {$item|ppljoin:", "}
                                    <br/>
                                {/foreach}
                                {foreach from=$shipment->getImportErrors() item=importError}
                                    {$importError}
                                    <br/>
                                {/foreach}
                            </div>
                        </td>
                    </tr>
                {/if}

            </table>
            {if $shipment->getImportState() === "Complete" || $shipment->getPackages()|pplpackageerrors}
                {if $shipment->getBatchLabelGroup()}
                    <br/>
                    <table>
                    <tr>
                        <td>Všechny zásilky na objednávce: &nbsp;</td>
                        <td style="vertical-align: center">
                            <a id="ppl_reference_{$shipment->getReferenceId()|urlencode}"
                                    data-shipmentId="{$shipment->getId()}"
                                    class="button btn-sm btn-secondary ppl-label-download" target="_blank"
                                    type="button"
                                    href="{$shipment|pplfileurl}">
                                Tisk
                            </a>
                            {if isset($selectedPrint) && $selectedPrint || $shipment->getPrintState()}
                                {if $shipment->getPrintState()}
                                    <span>{$shipment->getPrintState()|pplprintlabel}</span>
                                {else}
                                    <span style="position: relative; top: 5px">{$selectedPrint->getTitle()}</span>
                                {/if}
                                <a style="position: relative;" class="ppl_available_print_setting" href="#"
                                   data-optionals="{$availablePrinters|ppljser|escape}"
                                        {if $shipment->getPrintState()}
                                       data-value="{$shipment->getPrintState()|escape}"
                                        {else}
                                       data-value="{$selectedPrint->getCode()|escape}"
                                        {/if}

                                   data-shipmentId="{$shipment->getId()}">Změnit</a>
                            {/if}
                        </td>
                    </tr>
                {/if}
            {foreach from=$shipment->getPackages() item=package key=packageKey}
                {assign var="packageKey" value=$packageKey+1}
                <tr>
                    <td>
                        {if $package->getShipmentNumber()}
                            <a href="https://www.ppl.cz/vyhledat-zasilku?shipmentId={$package->getShipmentNumber()|urlencode}">{$package->getShipmentNumber()}</a>
                        {/if}
                        {if $package->getReferenceId()}
                            (ref: {$package->getReferenceId()})
                        {/if}
                    </td>
                    <td style="vertical-align: middle;  line-height: 2.5em">
                        {if $package->getLabelId() &&  ($package->getPhase() eq "None" or $package->getPhase() eq "Order")}
                            <a data-orderId="{$orderId}"
                               type="btton"
                               data-shipmentId="{$shipment->getId()}"
                               data-packageId="{$package->getId()}"
                               id="ppl-order-panel-anchor-href-{$addId}"
                               class="btn btn-sm btn-primary ppl-label-download"
                               target="_blank"
                               href="{$shipment|pplfileurl:$package}">
                                Tisk
                            </a>
                            <button class="btn btn-sm btn-danger cancel-package"
                                    data-orderId="{$orderId}"
                                    data-shipmentId="{$shipment->getId()}"
                                    data-packageId="{$package->getId()}"
                                   >Zrušit tuto zásilku
                            </button>
                        {/if}
                        {if $package->getPhase() eq "Canceled"}
                            Zrušeno
                        {else}
                            {$package->getPhaseLabel()}
                        {/if}
                    </td>
                    <td style="color: red">{$package->getImportError()}</td>
                    <td style="color: red">{$package->getImportErrorCode()}</td>
                    {/foreach}
                </tr>
                </table>

            {/if}
            <hr class="button-divider">
            {if in_array($shipment->getImportState(), ["None", "error", ""], true)}
                <button class="button btn btn-sm btn-primary detail-shipment"
                        data-orderId="{$orderId}"
                        {if $shipment->getId()}data-shipmentId="{$shipment->getId()}"
                        data-shipment="{$shipment|ppljser|escape}"{/if}>Upravit zásilku
                </button>
            {/if}
            {if $shipment->getImportState()|in_array:["InProcess", "InProgress", "Accepted"]}
                <button disabled
                        class="button btn btn-sm btn-primary refresh-shipments-labels"
                        data-orderId="{$orderId}"
                        {if $shipment->isInitialized("id")}data-shipmentId="{$shipment->getId()}"{/if}>Probíhá příprava
                    etikety
                </button>
            {elseif $shipment->getImportState() eq 'Complete'}
                <button class="button btn btn-sm btn-secondary  refresh-shipments-states"
                        type="button"
                        data-orderId="{$orderId}"
                        {if $shipment->isInitialized("id")}data-shipmentId="{$shipment->getId()}" {/if}>Aktualizovat
                    stav zásilky
                </button>
            {elseif $shipment->getImportState() eq "Error" or not $shipmentsErrors[$key]}
                <button class="button btn btn-sm  btn-primary create-labels"
                        type="button"
                        data-orderId="{$orderId}"
                        {if ($shipment->getId())}data-shipmentId="{$shipment->getId()}"{/if}
                        data-shipment="{$shipment|ppljser|escape}">Tisk etiket{if $shipment->getBatchId()} (#{$shipment->getBatchId()}){/if}
                </button>
                <button class="button btn btn-sm  btn-primary create-labels-add"
                        type="button"
                        data-orderId="{$orderId}"
                        {if ($shipment->getId())}data-shipmentId="{$shipment->getId()}"{/if}
                        data-shipment="{$shipment|ppljser|escape}">Přidat k tisku
                </button>
            {/if}
            {if $shipment->getId() and in_array($shipment->getImportState(), [ "Error","None",""], true)}
                <button class="button  btn btn-sm btn-danger remove-shipment"
                        type="button"
                        data-orderId="{$orderId}"
                        data-shipmentId="{$shipment->getId()}">Odstranit
                </button>
            {/if}
            {if $batchs[$key]}
                <a href="{$batchs[$key]}" class="button btn btn-sm  btn-primary"
                        type="button">Tisková davka
                </a>
            {/if}
            {if ($key !== $shipments|pplarray_key_last)}
                <hr style="margin-left: -12px; margin-right:-12px"/>
            {/if}

        {/foreach}
    {/if}
    {if $newShipment}
        <button class="button btn btn-sm btn-primary detail-shipment"
                data-orderId="{$orderId}"
                data-shipment="{$newShipment|ppljser|escape}">Přidat zásilku
        </button>
    {/if}
    <script type="text/javascript">
        adminOrder({$orderId})
    </script>
</div>