<?php
/**
 * Settings controller for the calendar module
 *
 * @author Jacob van Dam <j.vandam@jvdict.com>
 */

class BackendCalendarSettings extends BackendBaseActionEdit
{
	public function execute()
	{
		parent::execute();
		$this->loadForm();
		$this->validateForm();
		$this->parse();
		$this->display();
	}

	private function loadForm()
	{
		$radio_values = array(
			array(
				'label' => BL::getLabel('no'),
				'value' => 0,
			),
			 array(
				 'label' => BL::getLabel('yes'),
				 'value' => 1
			 )
		);

		$this->frm = new BackendForm('settings');
		$this->frm->addText('items_per_page', $this->getSetting('items_per_page', 10));
		$this->frm->addText('items_in_widget', $this->getSetting('items_in_widget', 5));
		$this->frm->addText('google_maps_key', $this->getSetting('google_maps_key', ''));
		$this->frm->addRadiobutton('use_google_maps', $radio_values, $this->getSetting('use_google_maps', 0));
	}

	private function validateForm()
	{
		if ($this->frm->isSubmitted()) {
			if ($this->frm->isCorrect()) {
				$this->setSetting('items_per_page', intval($this->frm->getField('items_per_page')->getValue()));
				$this->setSetting('items_in_widget', intval($this->frm->getField('items_in_widget')->getValue()));
				$this->setSetting('google_maps_key', $this->frm->getField('google_maps_key')->getValue());
				$this->setSetting('use_google_maps', intval($this->frm->getField('use_google_maps')->getValue()));
			}
		}
	}

	public function parse()
	{
		parent::parse();
	}

	/**
	 * Get a setting from the module
	 *
	 * @param string $key
	 * @param mixed $default_value
	 *
	 * @return mixed
	 */
	private function getSetting($key, $default_value)
	{
		return BackendModel::getModuleSetting($this->getModule(), $key, $default_value);
	}

	/**
	 * Set a new setting for this module
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return void
	 */
	private function setSetting($key, $value) {
		BackendModel::setModuleSetting($this->getModule(), $key, $value);
	}

}