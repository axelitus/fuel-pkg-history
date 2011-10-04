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
		// The name of the driver to be used (optional, default = 'file'). Options: file|database|session
		'name' => 'file',
		// Whether to encode the entries (using Fuel\Crypt class) or not (optional, default = true)
		'secure' => true,
		// The length of the hash to be used to identify the stack
		'hash_length' => 8,
		// Contains the specific config for the driver (other drivers will ignore it). This can be ommited when using other drivers
		'file' => array(
			// The path to the file to be used for archiving. (optional, default = sys_get_temp_dir())
			'path' => '',
			// The prefix used for the filenames
			'prefix' => 'his_',
			// The file extension to be used (you must include the dot)
			'extension' => '.tmp',
		),
		'database' => array(
			// The table name to be used for storing the stack
			'table' => 'history',
		),
		'session' => array(
		),
		// Garbage Collector options for driver
		'gc' => array(
			// Seconds that will last the files unmodified before the garbage collector deletes them (optional, default = 900 [15 minutes])
			'threshold' => 900,
			// probability % (between 0 and 100) for garbage collection
			'probability' => 5
		)
	),
	'entries' => array(
		// How many entries should we collect? (optional, default = 15, use 0 for unlimited)
		'limit' => 15,
		// Do not allow duplicate entries by refresh (optional, default = true)
		'prevent_refresh' => true
	)
);
// @formatter:on
