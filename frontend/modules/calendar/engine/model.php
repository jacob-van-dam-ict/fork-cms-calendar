<?php
/**
 * The frontend model file for the calendar module
 *
 * @author  Jacob van Dam <j.vandam@jvdict.nl>
 */
class FrontendCalendarModel
{
	/**
	 * Get an item by its revision
	 *
	 * @param $url
	 * @param $revision
	 *
	 * @return array
	 */
	public static function getRevision($url, $revision)
	{
		$return = (array) FrontendModel::getDB()->getRecord(
			'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.start) AS start, UNIX_TIMESTAMP(i.end) AS end,
				m.title AS meta_title,
				m.title_overwrite AS meta_title_overwrite,
				m.description AS meta_description,
				m.description_overwrite AS meta_description_overwrite,
				m.keywords AS meta_keywords,
				m.keywords_overwrite AS meta_keywords_overwrite,
				m.data,
				m.url
			 FROM calendar_events i
			 INNER JOIN meta m ON m.id = i.meta_id
			 WHERE i.language = ? AND i.revision_id = ? AND m.url = ?',
			array(FRONTEND_LANGUAGE, (int) $revision, (string) $url)
		);

		$return['meta_data'] = unserialize($return['data']);
		return $return;
	}

	/**
	 * Get an item by url
	 *
	 * @param $url
	 * @return array
	 */
	public static function get($url)
	{
		$return = (array) FrontendModel::getDB()->getRecord(
			'SELECT i.*, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.start) AS start, UNIX_TIMESTAMP(i.end) AS end,
				m.title AS meta_title,
				m.title_overwrite AS meta_title_overwrite,
				m.description AS meta_description,
				m.description_overwrite AS meta_description_overwrite,
				m.keywords AS meta_keywords,
				m.keywords_overwrite AS meta_keywords_overwrite,
				m.data,
				m.url
			 FROM calendar_events i
			 INNER JOIN meta m ON m.id = i.meta_id
			 WHERE i.language = ? AND i.status = ? AND i.hidden = ? AND m.url = ? AND i.publish_on <= NOW()',
			array(FRONTEND_LANGUAGE, 'active', 'N', (string) $url)
		);
		$return['meta_data'] = unserialize($return['data']);

		return $return;
	}

	/**
	 * Get all the items based on the limit and offset
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public static function getAll($limit = 10, $offset = 0)
	{

		$items = FrontendModel::getDB()->getRecords(
			'SELECT i.id, i.revision_id, i.language, i.title, UNIX_TIMESTAMP(i.publish_on) AS publish_on, UNIX_TIMESTAMP(i.start) AS start,
			m.url
			FROM calendar_events i
			INNER JOIN meta m ON m.id = i.meta_id
			WHERE i.status = ? AND i.hidden = ? AND i.language = ? AND i.publish_on <= NOW() AND end >= NOW()
			ORDER BY start ASC
			LIMIT ?, ?',
			array('active', 'N', FRONTEND_LANGUAGE, $offset, $limit)
		);

		$link = FrontendNavigation::getURLForBlock('calendar', 'detail');

		foreach ($items as $key => $item) {
			$items[$key]['full_url'] = $link.'/'.$item['url'];
		}

		return $items;
	}

	/**
	 * Count all the calendar events
	 *
	 * @return int
	 */
	public static function getAllCount()
	{
		return (int) FrontendModel::getDB()->getVar(
			'SELECT COUNT(i.id) AS count
			FROM calendar_events i
			WHERE i.status = ? AND i.language = ? AND i.hidden = ? AND i.publish_on <= ?',
			array('active', FRONTEND_LANGUAGE, 'N', FrontendModel::getUTCDate('Y-m-d H:i') . ':00'));
	}
}