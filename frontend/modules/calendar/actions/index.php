<?php
/**
 * The index action for the calendar module
 *
 * @author Jacob van Dam <j.vandam@jvdict.nl>
 */
class FrontendCalendarIndex extends FrontendBaseBlock
{
	public function execute()
	{
		parent::execute();
		$this->loadTemplate();
		$this->getData();
		$this->parse();
	}

	private function getData()
	{
		$current_page = $this->URL->getParameter('page', 'int', 1);

		// Pagination
		$this->pagination['url'] = FrontendNavigation::getURLForBlock('calendar');
		$this->pagination['limit'] = FrontendModel::getModuleSetting('calendar', 'overview_num_items', 10);
		$this->pagination['num_items'] = FrontendCalendarModel::getAllCount();
		$this->pagination['num_pages'] = (int) ceil($this->pagination['num_items']/$this->pagination['limit']);

		if ($this->pagination['num_pages'] == 0) {
			$this->pagination['num_pages'] = 1;
		}

		if ($current_page > $this->pagination['num_pages'] || $current_page < 1) {
			$this->redirect(FrontendNavigation::getURL(404));
		}

		$this->pagination['requested_page'] = $current_page;
		$this->pagination['offset'] = ($current_page*$this->pagination['limit'])-$this->pagination['limit'];

		$this->items = FrontendCalendarModel::getAll($this->pagination['limit'], $this->pagination['offset']);
	}

	private function parse()
	{
		$this->addCSS('screen.css');
		$this->tpl->assign('items', $this->items);
		$this->tpl->assign('current_language', FRONTEND_LANGUAGE);
		$this->tpl->assign('date_format', FrontendModel::getModuleSetting('core', 'date_format_short'));
		$this->parsePagination();
	}
}