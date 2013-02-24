<?php

class BackendCalendarIndex extends BackendBaseActionIndex
{
	/**
	 * @var BackendDataGridDB $events Hold the events
	 */
	private $events = null;

	/**
	 * Execute the current action
	 *
	 * @return void
	 */
	public function execute()
	{
		parent::execute();

		$this->loadGrid();
		$this->parse();
		$this->display();
	}

	/**
	 * Loads the grid with all the events
	 *
	 * @return void
	 */
	private function loadGrid()
	{
		$this->events = new BackendDataGridDB(BackendCalendarModel::QUERY_DATAGRID_ACTIVE, array('active', BL::getWorkingLanguage()));
		$this->events->setHeaderLabels(array('user_id' => SpoonFilter::ucfirst(BL::getLabel('Author')), 'publish_on' => SpoonFilter::ucfirst(BL::lbl('PublishedOn'))));
		$this->events->setColumnHidden('revision_id');

		// Add some sorting
		$this->events->setSortingColumns(array('user_id', 'publish_on', 'title'));
		$this->events->setSortParameter('desc');

		// Add a function to the columns
		$this->events->setColumnFunction(array('BackendDataGridFunctions', 'getUser'), array('[user_id]'), 'user_id');
		$this->events->setColumnFunction(array('BackendDataGridFunctions', 'getLongDate'), array('[publish_on]'), 'publish_on');

		$this->events->setRowAttributes(array('id' => 'row-[revision_id]'));

		// Valid is allowed to edit
		if (BackendAuthentication::isAllowedAction('edit')) {
			$this->events->setColumnURL('title', BackendModel::createURLForAction('edit').'&amp;id=[id]');
			$this->events->addColumn('edit', null, SpoonFilter::ucfirst(BL::getLabel('edit')), BackendModel::createURLForAction('edit').'&amp;id=[id]', SpoonFilter::ucfirst(BL::getLabel('edit')));
		}
	}

	/**
	 * Parse all the data
	 *
	 * @return void
	 */
	protected function parse()
	{
		parent::parse();

		$this->tpl->assign('events', ($this->events->getNumResults() != 0 ? $this->events->getContent() : false));
		$this->tpl->assign('showCalendarAdd', BackendAuthentication::isAllowedAction('add'));
	}
}