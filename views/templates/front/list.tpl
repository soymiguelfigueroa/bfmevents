{extends file=$layout}

{block name='content'}
    <h1>{l s='Our events' mod='bfmevents'}</h1>

    {foreach from=$events item=event key=key}
        <div class="row">
            <div class="col-xs-12">
                <a href="/bfmevents/show/{$event.id_event}">
                    <img src="{$module_image_dir|cat:$event.image}" class="img-responsive img-thumbnail">
                </a>
            </div>
        </div>
    {/foreach}
{/block}