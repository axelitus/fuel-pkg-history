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

namespace History;

// @formatter:off
/**
 * HistoryException
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
class HistoryException extends \FuelException {}
// @formatter:on

/**
 * History
 *
 * @package     Fuel
 * @subpackage  History
 * @version     1.1
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
class History
{
	/**
	 * @var string The version of the History package
	 */
	const VERSION = '1.1';

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
	protected static $_current = -1;

	/**
	 * @var int Contains the index for the previous History_Entry
	 */
	protected static $_previous = -2;

	/**
	 * @var bool prevent double-initiaization because of the 'core namespace' alias
	 */
	protected static $_initialized = false;

	// @formatter:off
	/**
	 * @var array of global config defaults
	 */
	protected static $_config_defaults = array(
		'history_id' => 'history',
		'driver' => array(
			'name' => 'file',
			'compression' => array(
				'active' => true,
				'format' => 'zlib',
				'level' => 5
			),
			'secure' => false,
			'hash_length' => 8,
			'file' => array(
				'path' => '',
				'prefix' => 'his_',
				'extension' => '.tmp'
			),
			'database' => array(
				'table' => 'history',
				'auto_create' => true
			),
			'session' => array(
			),
			'gc' => array(
				'active' => true,
				'threshold' => 900,
				'probability' => 5
			)
		),
		'entries' => array(
			'limit' => 15,
			'prevent_refresh' => true,
			'use_full_post' => false,
			'exclude' => array()
		)
	);
	// @formatter:on

	/**
	 * Prevent direct instatiation
	 */
	protected function __construct()
	{
		
	}
	
	/**
	 * Initializes the History Class
	 */
	public static function _init()
	{
		// See if the class has already been initialized
		if (!static::$_initialized)
		{
			// Load the config options
			static::_load_config();

			// If no other supported driver is loaded then set the driver to file
			static::$_config['driver']['name'] = ((static::$_config['driver']['name'] == 'database' || static::$_config['driver']['name'] == 'session') ? static::$_config['driver']['name'] : 'file');

			// Load the driver
			$driver = 'History_Driver_' . ucwords(static::$_config['driver']['name']);

			if (class_exists($driver))
			{
				static::$_driver = $driver::forge(static::$_config['history_id'], static::$_config['driver']);

				// Verify if a driver has been loaded
				if (is_object(static::$_driver) && static::$_driver instanceof History_Driver)
				{
					// Log Info
					\Log::info(get_called_class() . "::_init() - The specified driver " . static::$_config['driver']['name'] . " ({$driver}) was loaded. ");

					// Load previous entries using the driver
					static::load();

					// Initialization completed
					static::$_initialized = true;
				}
				else
				{
					// If the driver hasn't been loaded we cannot continue!
					throw new HistoryException("The specified driver " . static::$_config['driver']['name'] . " ({$driver}) could not be loaded!");
				}
			}
			else
			{
				// If we cannot find the driver we cannot continue!
				throw new HistoryException("The specified driver " . static::$_config['driver']['name'] . " ({$driver}) was not found!");
			}
		}
	}

	public static function _load_config()
	{
		// Load the config file
		\Config::load('history', true);
		static::$_config = \Arr::merge(static::$_config_defaults, \Config::get('history'));
		
		// If there are special routes in the entries.exclude array then change them
		if(!empty(static::$_config['entries']['exclude']))
		{
			// Look for all this special routes
			$special_routes = array('_root_', '_404_');
			foreach($special_routes as $special_route)
			{
				// Look for special route and change it
				if(($key = array_search($special_route, static::$_config['entries']['exclude'])) !== false)
				{
					$route = array_key_exists($special_route, \Router::$routes) ? \Router::$routes[$special_route]->translation : \Config::get('routes.'.$special_route);
					static::$_config['entries']['exclude'][$key] = $route;
					
					// If it's _root_ include an empty uri too and a controller-only uri if the 2nd segment is index
					if($special_route == '_root_' && !in_array('', static::$_config['entries']['exclude']))
					{
						$uri = new \Uri($route);
						if(($controller = $uri->get_segment(1)) !== null && $uri->get_segment(2) == 'index')
						{
							static::$_config['entries']['exclude'][] = $controller;
						}
						static::$_config['entries']['exclude'][] = '';
					}
				}
			}
		}
	}

	/**
	 * Pushes a History_Entry to the History stack
	 */
	public static function push(History_Entry $entry)
	{
		// Verify that the uri is not in the exclude array
		$uri = $entry->uri;
		if(in_array($uri, static::$_config['entries']['exclude']))
		{
			$uri = ($uri == '')? '_root_' : $uri;
			
			// Log Info
			\Log::info(get_called_class() . "::push() - The entry's uri '{$uri}' is set to be excluded so it will not be registered.");

			return;
		}
		
		// Use the prevent flag when set to prevent refresh entries
		if (static::$_config['entries']['prevent_refresh'] && static::$_current >= 0 && $entry->equals(static::current()))
		{
			$uri = ($uri == '')? '_root_' : $uri;
			
			// Log Info
			\Log::info(get_called_class() . "::push() - Refresh entry detected with uri: '{$uri}'! The options are set to ommit those entries so it will not be registered.");

			return;
		}

		// Push the new entry
		static::$_entries[] = $entry;
		static::$_previous = static::$_current++;

		// Prune the array if needed
		static::_prune();
	}

	/**
	 * Pushes a History_Entry based on a \Fuel\Core\Request to the History stack
	 */
	public static function push_request(\Fuel\Core\Request $request)
	{
		static::push(History_Entry::from_request($request, static::$_config['entries']['use_full_post']));
	}

	/**
	 * Pops the top-most (current) History_Entry from the History stack.
	 * Note: this will shorten the entries by one element. Returns null if no entry is found in the stack.
	 *
	 * @return null|History_Entry
	 */
	public static function pop()
	{
		$return = null;

		if (static::$_current >= 0)
		{
			$return = static::$_entries[static::$_current];
			unset(static::$_entries[static::$_current]);
			static::$_current = static::$_previous--;
		}

		return $return;
	}

	/**
	 * Prunes the array if needed depending on the limit config value.
	 */
	private static function _prune($force_pointers = false)
	{
		$pruned = false;
		if (($limit = static::$_config['entries']['limit']) > 0 && ($offset = count(static::$_entries) - $limit) > 0)
		{
			// Prune the array
			static::$_entries = array_slice(static::$_entries, $offset);

			// Log Info
			\Log::info(get_called_class() . "::_prune() - The history stack was pruned because the limit of " . static::$_config['entries']['limit'] . " entries was reached.");

			$pruned = true;
		}

		if ($pruned || $force_pointers)
		{
			// Set pointers to the correct values
			static::_set_pointers();
		}
	}

	/**
	 * Recalculates and sets the pointers to their correct values (mainly used after
	 * pruning)
	 */
	private static function _set_pointers()
	{
		if (($count = count(static::$_entries)) > 0)
		{
			static::$_current = $count - 1;
		}
		else
		{
			static::$_current = -1;
		}
		static::$_previous = static::$_current - 1;

		// Log Info
		\Log::info(get_called_class() . "::_set_pointers() - Pointers were recalculated.");
	}

	/**
	 * Gets the entries as an array
	 *
	 * @return array of History_Entry objects
	 */
	public static function get_entries()
	{
		return static::$_entries;
	}

	/**
	 * Gets the entry at the specified index. Returns null if index is out of bounds
	 *
	 * @return null|History_Entry
	 */
	public static function get_entry($index)
	{
		$return = null;
		if ($index >= 0 && $index < count(static::$_entries))
		{
			$return = static::$_entries[$index];
		}

		return $return;
	}

	/**
	 * Gets the history entries count
	 *
	 * @return int count of history entries
	 */
	public static function count()
	{
		return count(static::$_entries);
	}

	/**
	 * Gets the current History entry. Returns null if the stack is empty.
	 *
	 * @return null|History_Entry
	 */
	public static function current()
	{
		return static::get_entry(static::$_current);
	}

	/**
	 * Gets the previous History entry. Returns null if no previous entry s found in the stack.
	 *
	 * @return null|History_Entry
	 */
	public static function previous()
	{
		return static::get_entry(static::$_previous);
	}

	/**
	 * Loads the stored entries to the History stack using the configured driver
	 *
	 * @return bool true if correctly loaded | false if not loaded (Driver missing?)
	 */
	public static function load()
	{
		$return = false;

		// Verify if a driver has been loaded
		if (is_object(static::$_driver) && static::$_driver instanceof History_Driver)
		{
			// Load the driver. This will fill the entries array
			static::$_entries = static::$_driver->load();

			// Log Info
			\Log::info(get_called_class() . "::load() - The entries were loaded using the specified driver.");

			// Prune as needed. This will re-calculate the pointers too
			static::_prune(true);

			$return = true;
		}

		return $return;
	}

	/**
	 * Saves the History to a store using the configured driver
	 *
	 * @return bool true
	 */
	public static function save()
	{
		// Verify if a driver has been loaded
		if (is_object(static::$_driver) && static::$_driver instanceof History_Driver)
		{
			// Use the driver to store the history information.
			$return = static::$_driver->save(static::$_entries);

			if ($return)
			{
				// Log Info
				\Log::info(get_called_class() . "::save() - The entries were saved using the specified driver.");
			}
			else
			{
				// Log Info
				\Log::error(get_called_class() . "::save() - The entries could not be saved using the specified driver.");
			}
		}

		return $return;
	}

}
