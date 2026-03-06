<div class="col-md-12">
    <div class="col-md-12 form-group">
        <div><h2>PPL omezení</h2></div>
        {foreach from=$pplform item=value}
            {if $value["type"] === 'checkbox'}
                <div>
                <label><input type="checkbox" value="1" name="{$value['name']}" {if $value['value'] === true}checked{/if}> {$value['label']}</label>
                </div>
            {elseif $value['type'] === 'choices'}
                <div>
                <label>Zakázaná doprava<br/>
                    <select name="{$value['name']}[]" multiple>
                        {foreach from=$value['choices'] item=choice key=choiceKey}
                            <option  value="{$choiceKey}" {if $choiceKey|pplinarray:$value['value']}selected{/if}>{$choice}</option>
                        {/foreach}
                    </select>
                </label>
                </div>
            {elseif $value['type'] === 'sizes'}
                <div>
                    <input type="hidden" class="ppl-sizes-data" name="{$value['name']}" value="{if isset($value['value'])}{$value['value']|@json_encode|escape:'html':'UTF-8'}{else}[]{/if}" data-label="{$value['label']|escape:'html':'UTF-8'}">
                </div>
            {/if}

        {/foreach}
    </div>
</div>