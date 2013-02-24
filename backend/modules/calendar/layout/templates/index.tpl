{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblCalendar|ucfirst}: {$lblEvents}</h2>

	{option:showCalendarAdd}
		<div class="buttonHolderRight">
			<a href="{$var|geturl:'add'}" class="button icon iconAdd" title="{$lblAdd|ucfirst}">
			<span>{$lblAdd|ucfirst}</span>
		</a>
		</div>
	{/option:showCalendarAdd}
</div>

{option:events}
<div class="dataGridHolder">
	<div class="tableHeading">
		<h3>{$lblPublishedEvents|ucfirst}</h3>
	</div>
	{$events}
</div>
{/option:events}

{option:!events}
	<p>{$msgNoItems|sprintf:{$var|geturl:'add'}}</p>
{/option:!events}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}