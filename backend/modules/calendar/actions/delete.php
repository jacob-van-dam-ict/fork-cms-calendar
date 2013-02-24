<?php
/**
 * This action will delete a calendar event
 *
 * @author Jacob van Dam <j.vandam@jvdict.nl>
 */
class BackendCalendarDelete extends BackendBaseActionDelete
{
	public function execute()
	{
		$id = $this->getParameter('id', 'int');

		if ($id !== null && BackendCalendarModel::exists($id)) {
			parent::execute();

			$record = BackendCalendarModel::get($id);
			BackendCalendarModel::delete($record['id']);
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $id));

			if (is_callable(array('BackendSearchModel', 'removeIndex'))) {
				BackendSearchModel::removeIndex($this->getModule(), $id);
			}
			$this->redirect(BackendModel::createURLForAction('index').'&report=deleted&var='.$record['title']);
		} else {
			$this->redirect(BackendModel::createURLForAction('index').'&error=non-existing');
		}
	}
}
