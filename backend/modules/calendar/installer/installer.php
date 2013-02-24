<?php

class CalendarInstaller extends ModuleInstaller
{
	private $module_name = 'calendar';

	public function install()
	{
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');
		$this->addModule($this->module_name);
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// Set some settings for the calendar
		$this->setSetting($this->module_name, 'items_per_page', 10);
		$this->setSetting($this->module_name, 'items_in_widget', 5);
		$this->setSetting($this->module_name, 'use_google_maps', 0);
		$this->setSetting($this->module_name, 'google_maps_key', '', true);

		$this->makeSearchable($this->module_name);

		$this->setModuleRights(1, $this->module_name);

		// Set the rights
		$this->setModuleRights(1, $this->module_name, 'add');
		$this->setModuleRights(1, $this->module_name, 'edit');
		$this->setModuleRights(1, $this->module_name, 'delete');
		$this->setModuleRights(1, $this->module_name, 'index');
		$this->setModuleRights(1, $this->module_name, 'settings');

		// Set the navigation
		$navigationId = $this->setNavigation(null, 'Modules');
		$this->setNavigation($navigationId, 'Calendar', 'calendar/index', array('calendar/add', 'calendar/edit', 'calendar/delete'));

		// Add to the setting
		$settingId = $this->setNavigation(null, 'Settings');
		$modulesId = $this->setNavigation($settingId, 'Modules');
		$this->setNavigation($modulesId, 'Calendar', 'calendar/settings');

		// Add the extra
		$this->insertExtra($this->module_name, 'block', 'Calendar', null, null, 'N', 1000);
		$this->insertExtra($this->module_name, 'widget', 'UpcomingEvents', 'upcoming_events', null, 'N', 1001);
	}
}