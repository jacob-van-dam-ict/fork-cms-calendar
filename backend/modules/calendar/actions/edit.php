<?php

class BackendCalendarEdit extends BackendBaseActionEdit
{
	/**
	 * DataGrid for drafts
	 *
	 * @var BackendDataGrid
	 */
	private $dgDrafts;

	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		if (!is_null($this->id) && BackendCalendarModel::exists($this->id)) {
			parent::execute();
			$this->getData();
			$this->loadDrafts();
			$this->loadRevisions();
			$this->loadForm();
			$this->validate();
			$this->parse();
			$this->display();
		} else {
			$this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}
	}

	/**
	 * Load the data from the database
	 *
	 * @return void
	 */
	private function getData()
	{
		$this->record = BackendCalendarModel::get($this->id);

		// Check for revision
		$revision = $this->getParameter('revision', 'int');
		if (!is_null($revision)) {
			$this->record = BackendCalendarModel::getRevision($this->id, $revision);

			$this->tpl->assign('using_revision', true);
		}

		// Check for a draft
		$draft =$this->getParameter('draft', 'int');
		if (!is_null($draft)) {
			$this->record = BackendCalendarModel::getRevision($this->id, $draft);

			$this->tpl->assign('draft', $draft);
			$this->tpl->assign('using_draft', true);
		}

		if (empty($this->record)) {
			$this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
		}
	}

	/**
	 * Load the data grid with drafts
	 *
	 * @return void
	 */
	private function loadDrafts()
	{
		$this->dgDrafts = new BackendDataGridDB(BackendCalendarModel::QUERY_SPECIFIC_DRAFTS, array('draft', $this->record['id'], BL::getWorkingLanguage()));
		$this->dgDrafts->setColumnsHidden(array('id', 'revision_id'));
		$this->dgDrafts->setPaging(false);
		$this->dgDrafts->setHeaderLabels(array('user_id' => BL::getLabel('by'), 'edited_on' => BL::getLabel('LastEditedOn')));
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgDrafts->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');
		$this->dgDrafts->setRowAttributes(array('id' => 'row-[revision_id]'));

		if (BackendAuthentication::isAllowedAction('edit')) {
			$this->dgDrafts->setColumnURL('title', BackendModel::createURLForAction('edit').'&amp;id=[id]&amp;draft=[revision_id]');
			$this->dgDrafts->addColumn('use_draft', null, BL::lbl('UseThisDraft'), BackendModel::createURLForAction('edit').'&amp;id=[id]&amp;draft=[revision_id]', BL::lbl('UseThisDraft'));
		}
	}

	/**
	 * Load the data grid with revisions
	 *
	 * @return void
	 */
	private function loadRevisions()
	{
		$this->dgRevisions = new BackendDataGridDB(BackendCalendarModel::QUERY_SPECIFIC_DRAFTS, array('archived', $this->record['id'], BL::getWorkingLanguage()));
		$this->dgRevisions->setColumnsHidden(array('id', 'revision_id'));
		$this->dgRevisions->setPaging(false);
		$this->dgRevisions->setHeaderLabels(array('user_id' => BL::getLabel('by'), 'edited_on' => BL::getLabel('LastEditedOn')));
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->dgRevisions->setColumnFunction(array('BackendDataGridFunctions', 'getTimeAgo'), array('[edited_on]'), 'edited_on');
		$this->dgRevisions->setRowAttributes(array('id' => 'row-[revision_id]'));

		if (BackendAuthentication::isAllowedAction('edit')) {
			$this->dgRevisions->setColumnURL('title', BackendModel::createURLForAction('edit').'&amp;id=[id]&amp;revision=[revision_id]');
			$this->dgRevisions->addColumn('use_revision', null, BL::lbl('UseThisVersion'), BackendModel::createURLForAction('edit').'&amp;id=[id]&amp;revision=[revision_id]', BL::lbl('UseThisVersion'));
		}
	}

	/**
	 * Load the form
	 *
	 * @return void
	 */
	private function loadForm()
	{
		// Set hidden values
		$rbtHiddenValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'Y');
		$rbtHiddenValues[] = array('label' => BL::lbl('Published'), 'value' => 'N');

		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addText('location', $this->record['location']);
		$this->frm->addEditor('text', $this->record['description']);
		$this->frm->addEditor('introduction', $this->record['introduction']);
		$this->frm->addText('entrance', $this->record['entrance']);
		$this->frm->addText('minimum_age', $this->record['minimum_age']);
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, $this->record['hidden']);
		$this->frm->addDate('publish_on_date', $this->record['publish_on']);
		$this->frm->addTime('publish_on_time', date('H:i', $this->record['publish_on']));
		$this->frm->addDate('start_on_date', $this->record['start']);
		$this->frm->addTime('start_on_time', date('H:i', $this->record['start']));
		$this->frm->addDate('end_on_date', $this->record['end']);
		$this->frm->addTime('end_on_time', date('H:i', $this->record['end']));
		$this->frm->addText('tags', BackendTagsModel::getTags($this->URL->getModule(), $this->record['id']), null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addHidden('status', $this->record['status']);
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);

		$this->meta->setUrlCallback('BackendCalendarModel', 'getURL', array($this->record['id']));
	}

	/**
	 * Validate the form
	 *
	 * @return void
	 */
	private function validate()
	{
		if ($this->frm->isSubmitted()) {
			$this->frm->cleanupFields();

			// Add the validation
			$this->frm->getField('title')->isFilled(BL::err('TitleIsRequired'));
			$this->frm->getField('location')->isFilled(BL::err('LocationIsRequired'));
			$this->frm->getField('text')->isFilled(BL::err('TextIsRequired'));
			$this->frm->getField('introduction')->isFilled(BL::err('IntroductionIsRequired'));
			$this->frm->getField('publish_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('publish_on_time')->isValid(BL::err('TimeIsInvalid'));
			$this->frm->getField('start_on_time')->isValid(BL::err('TimeIsInvalid'));
			$this->frm->getField('start_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->frm->getField('end_on_time')->isValid(BL::err('TimeIsInvalid'));
			$this->frm->getField('end_on_date')->isValid(BL::err('DateIsInvalid'));
			$this->meta->validate();


			if ($this->frm->isCorrect()) {
				$event = array(
					'id' => $this->record['id'],
					'revision_id' => $this->record['revision_id'],
					'meta_id' => $this->meta->save(),
					'user_id' => BackendAuthentication::getUser()->getUserId(),
					'language' => BL::getWorkingLanguage(),
					'title' => $this->frm->getField('title')->getValue(),
					'description' => $this->frm->getField('text')->getValue(),
					'entrance' => $this->frm->getField('entrance')->getValue(),
					'minimum_age' => $this->frm->getField('minimum_age')->getValue(),
					'location' => $this->frm->getField('location')->getValue(),
					'introduction' => $this->frm->getField('introduction')->getValue(),
					'start' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('start_on_date'), $this->frm->getField('start_on_time'))),
					'end' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('end_on_date'), $this->frm->getField('end_on_time'))),
					'hidden' => $this->frm->getField('hidden')->getValue(),
					'publish_on' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time'))),
					'created_on' => $this->record['created_on'],
					'status' => SpoonFilter::getPostValue('status', array('active', 'draft'), 'active')
				);
				$event['edited_on'] = BackendModel::getUTCDate();

				$revision_id = BackendCalendarModel::update($event);
				BackendModel::triggerEvent($this->getModule(), 'after_add', array('event' => $event));
				BackendTagsModel::saveTags($event['id'], $this->frm->getField('tags')->getValue(), $this->URL->getModule());

				if($event['status'] == 'active') {
					BackendSearchModel::saveIndex($this->getModule(), $event['id'], array('title' => $event['title'], 'text' => $event['description'], 'introduction' => $event['introduction']));

					if(BackendModel::getModuleSetting($this->getModule(), 'ping_services', false)) BackendModel::ping(SITE_URL.BackendModel::getURLForBlock('calendar', 'detail').'/'.$this->meta->getURL());
					$this->redirect(BackendModel::createURLForAction('index').'&report=added&var='.urlencode($event['title']).'&highlight=row-'.$revision_id);
				} elseif($event['status'] == 'draft') {
					$this->redirect(BackendModel::createURLForAction('edit').'&report=saved-as-draft&var='.urlencode($event['title']).'&id='.$event['id'].'&draft='.$revision_id.'&highlight=row-'.$revision_id);
				}
			}
		}
	}

	/**
	 * Parse all the data and assign to the template
	 *
	 * @return void
	 */
	protected function parse()
	{
		parent::parse();

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		$this->record['url'] = $this->meta->getURL();
		$this->tpl->assign('event', $this->record);
		$this->tpl->assign('status', BL::lbl(SpoonFilter::ucfirst($this->record['status'])));

		$this->tpl->assign('revisions', ($this->dgRevisions->getNumResults() == 0 ? false : $this->dgRevisions->getContent()));
		$this->tpl->assign('drafts', ($this->dgDrafts->getNumResults() == 0 ? false : $this->dgDrafts->getContent()));

		$this->tpl->assign('show_delete', BackendAuthentication::isAllowedAction('delete'));

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detail_url', SITE_URL.$url);
	}
}