<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.0
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

// @formatter:off
return array(
	// default ID for history tag in Session (optional, default = 'history')
	'history_id' => 'history',
	// The driver to be used (optional, default File Driver config)
	'driver' => array(
		// The name of the driver to be used (optional, default = 'file')
		// Options: file|database|session
		'name' => 'file',
		// The path to the file to be used for archiving. (optional, default = sys_get_temp_dir())
		// For Database Driver this should be set to the table name. Using Session this will be ignored.
		'path' => ''
	),
	'entries' => array(
		// How many entries should we collect? (optional, default = 15, use 0 for unlimited)
		'limit' => 15,
		// Do not allow duplicate entries by refresh (optional, default = true)
		'prevent_refresh' => true
	)
);
// @formatter:on
