<div id="calendar_index">
	<h2>{$lblCalendar}</h2>
	{option:!items}
	{$msgNoEvents}
	{/option:!items}
	{option:items}
		<ul>
		{iteration:items}
			<li><a href="{$items.full_url}"><span class="date">{$items.start|date:'d-m-Y'}</span> {$items.title}</a></li>
		{/iteration:items}
        </ul>
	{/option:items}
</div>
{option:items}
{include:core/layout/templates/pagination.tpl}
{/option:items}