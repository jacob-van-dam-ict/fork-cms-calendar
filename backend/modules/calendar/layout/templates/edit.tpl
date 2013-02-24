{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblCalendar|ucfirst}: {$lblAdd}</h2>
    <div class="buttonHolderRight">
        <a href="{$detail_url}/{$event.url}{option:event.revision_id}?revision={$event.revision_id}{/option:event.revision_id}" class="button icon iconZoom previewButton targetBlank">
            <span>{$lblView|ucfirst}</span>
        </a>
    </div>
</div>

{form:edit}
<label for="title">{$lblTitle|ucfirst}</label>
	{$txtTitle} {$txtTitleError}

<div id="pageUrl">
	<div class="oneLiner">
		{option:detail_url}<p><span><a href="{$detail_url}">{$detail_url}/<span id="generatedUrl">{$event.url}</span></a></span></p>{/option:detail_url}
		{option:!detail_url}<p class="infoMessage">{$errNoModuleLinked}</p>{/option:!detail_url}
	</div>
</div>

<div class="tabs">
	<ul>
		<li><a href="#tabContent">{$lblContent|ucfirst}</a></li>
        <li><a href="#tabVersions">{$lblVersions|ucfirst}</a></li>
		<li><a href="#tabSEO">{$lblSEO|ucfirst}</a></li>
	</ul>

	<div id="tabContent">
		<table width="100%">
			<tr>
				<td id="leftColumn">

				{* Main content *}
					<div class="box">
						<div class="heading">
							<h3>
								<label for="text">{$lblMainContent|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
							</h3>
						</div>
						<div class="optionsRTE">
							{$txtText} {$txtTextError}
						</div>
					</div>

				{* Summary *}
					<div class="box">
						<div class="heading">
							<div class="oneLiner">
								<h3>
									<label for="introduction">{$lblSummary|ucfirst}</label>
								</h3>
								<abbr class="help">(?)</abbr>
								<div class="tooltip" style="display: none;">
									<p>{$msgHelpSummary}</p>
								</div>
							</div>
						</div>
						<div class="optionsRTE">
							{$txtIntroduction} {$txtIntroductionError}
						</div>
					</div>

				</td>

				<td id="sidebar">
					<div id="publishOptions" class="box">
						<div class="heading">
							<h3>{$lblDetails|ucfirst}</h3>
						</div>

						<div class="options">
							<p class="p0"><label for="publishOnDate">{$lblLocation|ucfirst}</label></p>
							<div class="oneLiner">
								<p>
									{$txtLocation} {$txtLocationError}
								</p>
							</div>
						</div>

						<div class="options">
							<p class="p0"><label for="startOnDate">{$lblStartOn|ucfirst}</label></p>
							<div class="oneLiner">
								<p>
									{$txtStartOnDate} {$txtStartOnDateError}
								</p>
								<p>
									<label for="startOnTime">{$lblAt}</label>
								</p>
								<p>
									{$txtStartOnTime} {$txtStartOnTimeError}
								</p>
							</div>
						</div>

						<div class="options">
							<p class="p0"><label for="endOnDate">{$lblEndOn|ucfirst}</label></p>
							<div class="oneLiner">
								<p>
									{$txtEndOnDate} {$txtEndOnDateError}
								</p>
								<p>
									<label for="endOnTime">{$lblAt}</label>
								</p>
								<p>
									{$txtEndOnTime} {$txtEndOnTimeError}
								</p>
							</div>
						</div>
                        <div class="options">
                            <p class="p0"><label for="entrance">{$lblEntrance|ucfirst}</label></p>
                            <div class="oneLiner">
                                <p>
									{$txtEntrance} {$txtEntranceError}
                                </p>
                                <p>
                                    <label for="minimumAge">{$lblMinimumAge|ucfirst}</label>
                                </p>
                                <p>
									{$txtMinimumAge} {$txtMinimumAgeError}
                                </p>
                            </div>
                        </div>
					</div>

					<div id="publishOptions" class="box">
						<div class="heading">
							<h3>{$lblStatus|ucfirst}</h3>
						</div>

						<div class="options">
							<ul class="inputList">
								{iteration:hidden}
									<li>
										{$hidden.rbtHidden}
										<label for="{$hidden.id}">{$hidden.label}</label>
									</li>
								{/iteration:hidden}
							</ul>
						</div>

						<div class="options">
							<p class="p0"><label for="publishOnDate">{$lblPublishOn|ucfirst}</label></p>
							<div class="oneLiner">
								<p>
									{$txtPublishOnDate} {$txtPublishOnDateError}
								</p>
								<p>
									<label for="publishOnTime">{$lblAt}</label>
								</p>
								<p>
									{$txtPublishOnTime} {$txtPublishOnTimeError}
								</p>
							</div>
						</div>
					</div>

					<div class="box" id="articleMeta">
						<div class="heading">
							<h3>{$lblMetaData|ucfirst}</h3>
						</div>
						<div class="options">
							<label for="tags">{$lblTags|ucfirst}</label>
							{$txtTags} {$txtTagsError}
						</div>
					</div>

				</td>
			</tr>
		</table>
	</div>

    <div id="tabVersions">
		{option:drafts}
            <div class="tableHeading">
                <div class="oneLiner">
                    <h3 class="oneLinerElement">{$lblDrafts|ucfirst}</h3>
                    <abbr class="help">(?)</abbr>
                    <div class="tooltip" style="display: none;">
                        <p>{$msgHelpDrafts}</p>
                    </div>
                </div>
            </div>

            <div class="dataGridHolder">
				{$drafts}
            </div>
		{/option:drafts}

        <div class="tableHeading">
            <div class="oneLiner">
                <h3 class="oneLinerElement">{$lblPreviousVersions|ucfirst}</h3>
                <abbr class="help">(?)</abbr>
                <div class="tooltip" style="display: none;">
                    <p>{$msgHelpRevisions}</p>
                </div>
            </div>
        </div>

		{option:revisions}
            <div class="dataGridHolder">
				{$revisions}
            </div>
		{/option:revisions}

		{option:!revisions}
            <p>{$msgNoRevisions}</p>
		{/option:!revisions}
    </div>

	<div id="tabSEO">
	{include:{$BACKEND_CORE_PATH}/layout/templates/seo.tpl}
	</div>
</div>

<div class="fullwidthOptions">
	{option:show_delete}
        <a href="{$var|geturl:'delete'}&amp;id={$event.id}" data-message-id="confirmDelete" class="askConfirmation button linkButton icon iconDelete">
            <span>{$lblDelete|ucfirst}</span>
        </a>

        <div id="confirmDelete" title="{$lblDelete|ucfirst}?" style="display: none;">
            <p>
				{$msgConfirmDelete|sprintf:{$event.title}}
            </p>
        </div>
	{/option:show_delete}

	<div class="buttonHolderRight">
		<input id="addButton" class="inputButton button mainButton" type="submit" name="add" value="{$lblPublish|ucfirst}" />
		<a href="#" id="saveAsDraft" class="inputButton button"><span>{$lblSaveDraft|ucfirst}</span></a>
	</div>
</div>
{$hidStatus}
{/form:edit}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}