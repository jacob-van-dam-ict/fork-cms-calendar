<?php

/**
 * View the calendar event detail
 *
 * @author Jacob van Dam <j.vandam@jvdict.nl>
 */

class FrontendCalendarDetail extends FrontendBaseBlock
{
	/**
	 * The calendar event
	 *
	 * @var array
	 */
	private $event = array();

	/**
	 * The settings for this module
	 *
	 * @var array
	 */
	private $settings = array();

	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	/**
	 * Load the current calendar event
	 *
	 * @return void
	 */
	private function getData()
	{
		if (($url = $this->URL->getParameter(1)) == null) {
			$this->redirect(FrontendNavigation::getURL(404), 404);
		}

		if (($revision = $this->URL->getParameter('revision', 'int')) != null) {
			$this->event = FrontendCalendarModel::getRevision($url, $revision);
			$this->header->addMetaData(array('name' => 'robots', 'content' => 'noindex, nofollow'), true);
		} else {
			$this->event = FrontendCalendarModel::get($url);
		}

		if (empty($this->event)) {
			$this->redirect(FrontendNavigation::getURL(404), 404);
		}

		$this->event['tags'] = FrontendTagsModel::getForItem('calendar', $this->event['id']);
		$this->settings = FrontendModel::getModuleSettings('calendar');
	}

	/**
	 * Parse the data for display
	 */
	private function parse()
	{
		//var_dump($this->event);
		$this->addCSS('screen.css');

		// Set the meta data
		$this->header->setPageTitle($this->event['meta_title'], ($this->event['meta_title_overwrite'] == 'Y'));
		$this->header->addMetaKeywords($this->event['meta_keywords'], ($this->event['meta_keywords_overwrite'] == 'Y'));
		$this->header->addMetaDescription($this->event['meta_description'], ($this->event['meta_description_overwrite'] == 'Y'));


		if (isset($this->event['meta_data']['seo_index'])) {
			$this->header->addMetaData(array('name' => 'robots', 'content' => $this->event['meta_data']['seo_index']), true);
		}
		if (isset($this->event['meta_data']['seo_follow'])) {
			$this->header->addMetaData(array('name' => 'robots', 'content' => $this->event['meta_data']['seo_follow']), true);
		}

		$this->tpl->assign(
			array(
				'settings' => $this->settings,
				'event' => $this->event
			)
		);
	}

}