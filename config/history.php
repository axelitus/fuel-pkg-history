<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.1
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2011 Fuel Development Team
 * @link       http://fuelphp.com
 */

// @formatter:off
return array(
	// Default ID for history tag in Session (optional, default = 'history').
	'history_id' => 'history',
	// Contains the options for the driver.
	'driver' => array(
		// The name of the driver to be used (optional, default = 'file'). Options: file|database|session.
		'name' => 'file',
		// The compression options to be used.
		'compression' => array(
			// Whether to compress the data before encoding or not (optional, default = true).
			'active' => true,
			// The format of the compression to be used (optional, default = 'zlib'). Options: zlib|deflate.
			'format' => 'zlib',
			// The level of compression to use (optional, default = 5).
			'level' => 5
		),
		// Whether to encode the entries (using Fuel\Crypt class) or not (optional, default = true).
		'secure' => true,
		// The length of the hash to be used to identify the stack. The max length is 40 (optional, default = 8).
		'hash_length' => 8,
		// Contains the specific config for the file driver (other drivers will ignore it). This can be ommited when using other drivers.
		'file' => array(
			// The path to the file to be used for storing. (optional, default = '' [uses sys_get_temp_dir()]).
			'path' => '',
			// The prefix used for the filenames (optional, default = 'his_').
			'prefix' => 'his_',
			// The file extension to be used [you must include the dot] (optional, default = '.tmp').
			'extension' => '.tmp'
		),
		// Contains the specific config for the database driver (other drivers will ignore it). This can be ommited when using other drivers.
		'database' => array(
			// The table name to be used for storing the stack (optional, default = 'history').
			'table' => 'history',
			// Determines if tabel should be created if it does not exist (optional, default = true).
			'auto_create' => true
		),
		// Contains the specific config for the session driver (other drivers will ignore it). This can be ommited when using other drivers.
		'session' => array(
		),
		// Garbage Collector options for driver.
		'gc' => array(
			// Whether to use Garbage Collection or not. You should leave this on to prevent data flooding and even collisions (optional, default = true).
			'active' => true,
			// Seconds that will last the unmodified items (files or records depending on driver) before the garbage collector deletes them (optional, default = 900 [15 minutes]).
			'threshold' => 900,
			// Probability % (between 0 and 100) for garbage collection (optional, default = 5).
			'probability' => 5
		)
	),
	// Contains the options for the entries.
	'entries' => array(
		// How many entries should we collect? (optional, default = 15 [use 0 for unlimited]).
		'limit' => 15,
		// Do not allow duplicate entries by refresh (optional, default = true).
		'prevent_refresh' => true,
		// Whether to save the full post data in the entry or just the post data hash (optional, default = false).
		'use_full_post' => false,
		// Exclude
		'exclude' => array(
			'_root_',
			'_404_'
		)
	)
);
// @formatter:on
