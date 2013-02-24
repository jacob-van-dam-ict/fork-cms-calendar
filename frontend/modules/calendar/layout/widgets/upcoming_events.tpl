<div id="upcoming_events" class="widget">
	<h2>{$lblActivities}</h2>
	{option:upcoming_events}
	<ul>
		{iteration:upcoming_events}
		<li><a href="{$upcoming_events.full_url}"><span class="date">{$upcoming_events.start|date:'d-m-Y'}</span>{$upcoming_events.title}</a></li>
		{/iteration:upcoming_events}
	</ul>
	{/option:upcoming_events}
</div>