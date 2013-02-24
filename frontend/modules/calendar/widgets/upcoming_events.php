<?php
/**
 * Widget for the calendar which displays the upcomming events
 *
 * @author Jacob van Dam <j.vandam@jvdict.nl>
 */

class FrontendCalendarWidgetUpcomingEvents extends FrontendBaseWidget
{
	/**
	 * Events
	 *
	 * @var array
	 */
	private $events = array();

	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	private function getData()
	{
		$this->events = FrontendCalendarModel::getAll(FrontendModel::getModuleSetting($this->getModule(), 'items_in_widget', 5), 0);
	}

	private function parse()
	{
		$this->addCSS('upcoming_events.css');
		$this->tpl->assign('upcoming_events', $this->events);
	}
}