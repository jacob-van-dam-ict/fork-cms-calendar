<?php

class BackendCalendarAdd extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return void
	 */
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validate();
		$this->parse();
		$this->display();
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

		$this->frm = new BackendForm('add');
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addText('location');
		$this->frm->addEditor('text');
		$this->frm->addEditor('introduction');
		$this->frm->addRadiobutton('hidden', $rbtHiddenValues, 'N');
		$this->frm->addDate('publish_on_date');
		$this->frm->addTime('publish_on_time');
		$this->frm->addDate('start_on_date');
		$this->frm->addTime('start_on_time');
		$this->frm->addDate('end_on_date');
		$this->frm->addTime('end_on_time');
		$this->frm->addText('minimum_age');
		$this->frm->addText('entrance');
		$this->frm->addText('tags', null, null, 'inputText tagBox', 'inputTextError tagBox');
		$this->frm->addHidden('status', '');
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
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
					'id' => BackendCalendarModel::getNewId(),
					'meta_id' => $this->meta->save(),
					'user_id' => BackendAuthentication::getUser()->getUserId(),
					'language' => BL::getWorkingLanguage(),
					'title' => $this->frm->getField('title')->getValue(),
					'location' => $this->frm->getField('location')->getValue(),
					'description' => $this->frm->getField('text')->getValue(),
					'introduction' => $this->frm->getField('introduction')->getValue(),
					'minimum_age' => $this->frm->getField('minimum_age')->getValue(),
					'entrance' => $this->frm->getField('entrance')->getValue(),
					'start' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('start_on_date'), $this->frm->getField('start_on_time'))),
					'end' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('end_on_date'), $this->frm->getField('end_on_time'))),
					'hidden' => $this->frm->getField('hidden')->getValue(),
					'publish_on' => BackendModel::getUTCDate(null, BackendModel::getUTCTimestamp($this->frm->getField('publish_on_date'), $this->frm->getField('publish_on_time'))),
					'created_on' => BackendModel::getUTCDate(),
					'status' => SpoonFilter::getPostValue('status', array('active', 'draft'), 'active')
				);
				$event['edited_on'] = $event['created_on'];

				$revision_id = BackendCalendarModel::insert($event);
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

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detail_url', SITE_URL.$url);
	}
}