{extends file=$layout}

{block name='content'}
    <img src="{$event.image}" class="img-responsive img-thumbnail" />
    <div class="event-details">
        <div class="event-title">
            <span class="">{$event.since}</span>
            <h1>{$event.name}</h1>
        </div>
        <div class="event-body">
            <div class="grid">
                <div class="event-time">{$event.since} - {$event.until}</div>
                <div class="event-location">{$event.location}</div>
            </div>
            <div class="event-calendar">
                <a href="{$event.google_calendar_link}" target="_blank">Google Calendar</a>
            </div>
            <div class="event-get-directions">
                <form action="https://maps.google.com/maps" method="get" target="_blank">
                    <input type="hidden" name="daddr" value="Parking Lot Garnet B. Rickard Recreation Complex L1C 0K6">
                    <p class="evo_get_direction_content">
                        <input class="evoInput" type="text" name="saddr" placeholder="{l s='Type here your address to get directions' mod='bfmevents'}" value="">
                        <button type="submit" class="evo_get_direction_button evcal_btn dfx fx_ai_c" title="Click here to get directions"><i class="fa fa-road marr5"></i> Get Directions</button>
                    </p>
                </form>
            </div>
            <div class="event-description">
                <p>{$event.description}</p>
            </div>
        </div>
    </div>
{/block}