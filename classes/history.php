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

namespace History;

/**
 * History
 *
 * @package     Fuel
 * @subpackage  History
 */
class History
{
	/**
	 * @var array Contains the browser history for current browser session
	 */
	protected static $_entries = array();

	/**
	 * @var History_Driver holds the driver for storing purposes
	 */
	protected static $_driver = null;

	/**
	 * @var array Contains the configuration for the History class
	 */
	protected static $_config = array();

	/**
	 * @var int Contains the index for the current History_Entry
	 */
	public static $_current = -1;

	/**
	 * @var int Contains the index for the last History_Entry
	 */
	public static $_last = -2;

	// @formatter:off
	/**
	 * @var array of global config defaults
	 */
	protected static $_config_defaults = array(
		// default ID for history tag in Session (optional, default = 'history')
		'history_id' => 'history',
		// The driver to be used (optional, default File Driver config)
		'driver' => array(
			// The name of the driver to be used (optional, default = 'file')
			// Options: file|database|session
			'name' => 'file',
			// The path to the file to be used for archiving. (optional, default = sys_get_temp_dir ())
			// For Database Driver this should be set to the table name
			'path' => '',
			// Whether to encode the entries (Using Fuel\Crypt class) or not (optiona, default = true)
			'secure' => true
		),
		'entries' => array(
			// How many entries should we collect? (optional, default = 15, use 0 for unlimited)
			'limit' => 15,
			// Do not allow duplicate entries by refresh (optional, default = true)
			'filter_refresh' => true
		)
	);
	// @formatter:on

	/**
	 * Initializes the History Class
	 */
	public static function _init()
	{
		\Config::load('history', true);
		static::$_config = \Arr::merge_replace(static::$_config_defaults, \Config::get('history'));

		// If no other supported driver is loaded then set the driver to file
		static::$_config['driver']['name'] = ((static::$_config['driver']['name'] == 'database' || static::$_config['driver']['name'] == 'session') ? static::$_config['driver']['name'] : 'file'); 

		// Load the driver
		$driver = 'History_Driver_' . ucwords(static::$_config['driver']['name']);
		static::$_driver = $driver::forge(static::$_config['history_id'], static::$_config['driver']);
		
		// TODO: For file the path must exist, otherwise use sys_get_temp_dir() but log
		// an error. For Database check if table exist otherwise throw an exception.
		// This task is thrown at the drivers directly. I leave this todo to remember.

		// Load previous entries using the driver
		static::$_entries = static::$_driver->load();
	}

	/**
	 * Pushes a History_Entry
	 */
	public static function push(History_Entry $entry = null)
	{
		// TODO: use the prevent flag when set to prevent refresh entries
		static::$_entries[] = $entry;
		static::$_last = static::$_current++;
		
		// Prune the array if needed
		static::prune();
	}

	/**
	 * Pushes a History_Entry based on a \Fuel\Core\Request
	 */
	public static function push_request(\Fuel\Core\Request $request)
	{
		static::push(History_Entry::from_request($request));
	}

	/**
	 * Pops the top-most (current) History_Entry from the History
	 * Note: this will shorten the entries by one element.
	 * @return null|History_Entry
	 */
	public static function pop()
	{
		$return = null;
		if (static::$_current >= 0)
		{
			$return = static::$_entries[static::$_current];
			unset(static::$_entries[static::$_current]);
			static::$_current = static::$_last--;
		}

		return $return;
	}
	
	/**
	 * Prunes the array if needed depending on the limit config value.
	 */
	private static function prune()
	{
		// TODO: use the limit config to "prune" the array in case it needs to be
	}

	/**
	 * Gets the entries as an array
	 */
	public static function get_entries()
	{
		return static::$_entries;
	}

	/**
	 * Gets the history entries count
	 */
	public static function count()
	{
		return count(static::$_entries);
	}

	/**
	 * Gets the current History_Entry
	 */
	public static function current()
	{
		return (static::$_current >= 0) ? static::$_entries[static::$_current] : null;
	}

	/**
	 * Gets the last History_Entry
	 */
	public static function previous()
	{
		return (static::$_last >= 0) ? static::$_entries[static::$_current] : null;
	}

	/**
	 * Saves the History to the session
	 */
	public static function save()
	{
		// Use the driver to store the history information.
		static::$_driver->save(static::$_entries);
	}

	// === End: Interface Countable ===

}
