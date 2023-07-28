<div class="panel form-horizontal">
    <div class="panel-heading">{l s='Event' mod='bfmevents'}: {$event.name}</div>

    <div class="row">
        <div class="col-md-6">
            <h1>{l s='Data' mod='bfmevents'}</h1>
            <p><strong>{l s='Name' mod='bfmevents'}</strong>: {$event.name}</p>
            <p><strong>{l s='Since' mod='bfmevents'}</strong>: {$event.since}</p>
            <p><strong>{l s='Until' mod='bfmevents'}</strong>: {$event.until}</p>
            <p><strong>{l s='Location' mod='bfmevents'}</strong>: {$event.location}</p>
            <p><strong>{l s='Description' mod='bfmevents'}</strong>: {$event.description}</p>
            <p><strong>{l s='Active' mod='bfmevents'}</strong>: {$event.active}</p>
            <p><strong>{l s='Created' mod='bfmevents'}</strong>: {$event.created_at}</p>
            <p><strong>{l s='Updated' mod='bfmevents'}</strong>: {$event.updated_at}</p>
        </div>
        <div class="col-md-6">
            <h1>{l s='Image' mod='bfmevents'}</h1>
            <img src="{$module_image_dir|cat:$event.image}" alt="{l s='Image for'}: {$event.name}" class="img-thumbnail" />
        </div>
    </div>

    <div class="panel-footer">
        <a class="btn btn-default" id="czvr_bfmevents_form_cancel_btn" onclick="javascript:window.history.back();">
            <i class="process-icon-cancel"></i> {l s='Back' mod='bfmevents'}
        </a>
    </div>
</div>