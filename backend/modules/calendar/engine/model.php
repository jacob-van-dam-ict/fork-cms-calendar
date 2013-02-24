<?php

class BackendCalendarModel
{
	const QUERY_DATAGRID_ACTIVE =
		'SELECT i.hidden, i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, i.user_id
		 FROM calendar_events AS i
		 WHERE i.status = ? AND i.language = ?';

	const QUERY_SPECIFIC_DRAFTS =
		'SELECT i.id, i.revision_id, i.title, UNIX_TIMESTAMP(i.edited_on) AS edited_on, i.user_id
		 FROM calendar_events AS i
		 WHERE i.status = ? AND i.id = ? AND i.language = ?
		 ORDER BY i.edited_on DESC';

	/**
	 * @static
	 * Get the new id
	 *
	 * @return int
	 */
	public static function getNewId()
	{
		return (int) (self::getMaximumId()+1);
	}

	/**
	 * @static
	 * Get the current maximum id
	 *
	 * @return int
	 */
	public static function getMaximumId()
	{
		return (int) BackendModel::getDB()->getVar('SELECT MAX(id) FROM calendar_events LIMIT 1');
	}

	/**
	 * Check if the event exists.
	 *
	 * @static
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public static function exists($id)
	{
		return (bool) BackendModel::getDB()->getVar('SELECT id FROM calendar_events WHERE id = ? AND language = ?', array((int) $id, BL::getWorkingLanguage()));
	}

	/**
	 * Get an event by its id
	 *
	 * @static
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public static function get($id)
	{
		return (array) BackendModel::getDB()->getRecord('
			SELECT
				ce.*,
				UNIX_TIMESTAMP(ce.publish_on) AS publish_on,
				UNIX_TIMESTAMP(ce.start) AS start,
				UNIX_TIMESTAMP(ce.`end`) AS `end`,
				UNIX_TIMESTAMP(ce.created_on) AS created_on
			FROM
				calendar_events ce
			WHERE
				ce.id = ?
				AND
				ce.language = ?
				AND (ce.status = ? OR ce.status = ?)',
		array((int) $id, BL::getWorkingLanguage(), 'active', 'draft'));
	}

	/**
	 * Get an event by its id and revision
	 *
	 * @static
	 *
	 * @param int $id
	 * @param int $revision
	 *
	 * @return array
	 */
	public static function getRevision($id, $revision)
	{
		return (array) BackendModel::getDB()->getRecord('SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.created_on) AS created_on, UNIX_TIMESTAMP(i.edited_on) AS edited_on,
				UNIX_TIMESTAMP(i.start) AS start,
				UNIX_TIMESTAMP(i.`end`) AS `end`,m.url FROM calendar_events AS i INNER JOIN meta AS m ON m.id = i.meta_id WHERE i.id = ? AND i.revision_id = ? AND language = ?', array((int) $id, (int) $revision, BL::getWorkingLanguage()));
	}

	/**
	 * Insert an event to the calendar
	 *
	 * @param array $event
	 *
	 * @return int
	 */
	public static function insert(array $event)
	{
		$revision_id = BackendModel::getDB(true)->insert('calendar_events', $event);

		BackendModel::invalidateFrontendCache('calendar', BL::getWorkingLanguage());

		return $revision_id;
	}

	/**
	 * Update an existing event of the calendar
	 *
	 * @param array $event
	 *
	 * @return int
	 */
	public static function update(array $event)
	{
		if ($event['status'] == 'active') {
			BackendModel::getDB(true)->update('calendar_events', array('status' => 'archived'), 'id = ? AND status = ?', array($event['id'], $event['status']));
			$revision = self::getRevision($event['id'], $event['revision_id']);
			$event['created_on'] = BackendModel::getUTCDate('Y-m-d H:i:s', $revision['created_on']);
			if ($revision['status'] == 'draft') {
				BackendModel::getDB(true)->delete('calendar_events', 'id = ? AND status = ?', array($revision['id'], $revision['status']));
			}
		}

		unset($event['revision_id']);
		$rows_to_keep = BackendModel::getModuleSetting('calendar', 'max_num_revisions', 20);
		$archive_type = ($event['status'] == 'active' ? 'archived' : $event['status']);

		$ids_to_keep = (array) BackendModel::getDB()->getColumn(
			'SELECT i.revision_id
			 FROM calendar_events AS i
			 WHERE i.id = ? AND i.status = ? AND i.language = ?
			 ORDER BY i.edited_on DESC
			 LIMIT ?',
			array($event['id'], $archive_type, BL::getWorkingLanguage(), $rows_to_keep)
		);

		if(!empty($ids_to_keep)) {
			BackendModel::getDB(true)->delete('calendar_events', 'id = ? AND status = ? AND revision_id NOT IN (' . implode(', ', $ids_to_keep) . ')', array($event['id'], $archive_type));
		}
		$event['revision_id'] = BackendModel::getDB(true)->insert('calendar_events', $event);
		BackendModel::invalidateFrontendCache('calendar', BL::getWorkingLanguage());

		return $event['revision_id'];
	}

	/**
	 * Delete all the events with the given id
	 *
	 * @param integer $id
	 *
	 * @return void
	 */
	public static function delete($id)
	{
		BackendModel::getDB(true)->delete('calendar_events', 'id = ?', array((int) $id));
	}

	/**
	 * Retrieve the unique URL for an event
	 *
	 * @param string $URL The URL to base on.
	 * @param int[optional] $id The id of the item to ignore.
	 * @return string
	 */
	public static function getURL($URL, $id = null)
	{
		$URL = (string) $URL;

		// get db
		$db = BackendModel::getDB();

		// new item
		if ($id === null) {
			// already exists
			if ((bool) $db->getVar(
				'SELECT 1
				 FROM calendar_events AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $URL)))
			{
				$URL = BackendModel::addNumber($URL);
				return self::getURL($URL);
			}
		} else {
			// already exists
			if((bool) $db->getVar(
				'SELECT 1
				 FROM calendar_events AS i
				 INNER JOIN meta AS m ON i.meta_id = m.id
				 WHERE i.language = ? AND m.url = ? AND i.id != ?
				 LIMIT 1',
				array(BL::getWorkingLanguage(), $URL, $id)))
			{

				$URL = BackendModel::addNumber($URL);
				return self::getURL($URL, $id);
			}
		}

		return $URL;
	}
}