{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}
<div class="pageTitle">
    <h2>{$lblModuleSettings|ucfirst}: {$lblCalendar}</h2>
</div>

{form:settings}
<div class="box">
    <div class="heading">
        <h3>{$lblSettings|ucfirst}</h3>
    </div>
    <div class="options">
        <label for="itemsPerPage">{$lblItemsPerPage|ucfirst}</label>
		{$txtItemsPerPage} {$txtItemsPerPageError}
    </div>
    <div class="options">
        <label for="itemsInWidget">{$lblItemsInWidget|ucfirst}</label>
		{$txtItemsInWidget} {$txtItemsInWidgetError}
    </div>
    <div class="options">
        <label for="googleMapsKey">{$lblGoogleMapsKey|ucfirst}</label>
		{$txtGoogleMapsKey} {$txtGoogleMapsKeyError}
    </div>
</div>

<div class="box">
    <div class="heading">
        <h3>{$lblUseGoogleMaps|ucfirst}</h3>
    </div>
    <div class="options">
        <ul>
			{iteration:use_google_maps}
                <li>
					{$use_google_maps.rbtUseGoogleMaps}
                    <label for="{$use_google_maps.id}">{$use_google_maps.label}</label>
                </li>
			{/iteration:use_google_maps}
        </ul>
    </div>
</div>

<div class="fullwidthOptions">
    <div class="buttonHolderRight">
        <input id="save" class="inputButton button mainButton" type="submit" name="save" value="{$lblSave|ucfirst}" />
    </div>
</div>
{/form:settings}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}