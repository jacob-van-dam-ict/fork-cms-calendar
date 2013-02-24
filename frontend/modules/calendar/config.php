<?php
/**
 * Configuration for the frontend calendar module
 *
 * @author Jacob van Dam <j.vandam@jvdict.nl>
 */
class FrontendCalendarConfig extends FrontendBaseConfig
{
	/**
	 * The default action
	 *
	 * @var string
	 */
	protected $defaultAction = 'index';

	/**
	 * The disabled actions
	 *
	 * @var array
	 */
	protected $disabledActions = array();
}