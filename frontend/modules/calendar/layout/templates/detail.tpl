<div id="calendar_event">
	<h2>{$event.title}</h2>
	<ul class="event_header">
		<li><span>{$lblBegins|ucfirst}</span>{$event.start|date:'d-m-Y'} {$lblAt} {$event.start|date:'H:i'}</li>
        <li><span>{$lblEnds|ucfirst}</span>{$event.end|date:'d-m-Y'} {$lblAt} {$event.end|date:'H:i'}</li>
        <li><span>{$lblLocation|ucfirst}</span>{$event.location}</li>
        <li><span>{$lblEntrance|ucfirst}</span>{$event.entrance}</li>
        <li><span>{$lblMinimumAge|ucfirst}</span>{$event.minimum_age}</li>
	</ul>
	<div class="event_content">{$event.description}</div>
	<p class="event_footer"><a href="{$var|geturlforblock:'calendar'}"><< {$msgBackToNewsPage|ucfirst}</a></p>
</div>