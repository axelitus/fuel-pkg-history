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
abstract class History_Driver
{
	/**
	 * @var string Contains the History's class history_id (the key in Session)
	 */
	protected $_history_id = 'history';

	/**
	 * @var array Contains the driver configuration
	 */
	protected $_config = array();
	
	/**
	 * @var History_Driver_GC Garbage Collector specific for driver if needed 
	 */
	protected $_gc = null;

	// @formatter:off
	/**
	 * @var array of global config defaults
	 * Can be overloaded in derived class but PAY ATTENTION, otherwise something will
	 * break awfully. If this happens take look here first. These are the default
	 * values that should be present, unless your driver doesn't need them.
	 */
	protected static $_config_defaults = array(
		'name' => 'file',
		'secure' => true,
		'file' => array(
			'path' => '',
			'prefix' => 'his_',
			'extension' => 'tmp',
			
		),
		'database' => array(
			'table' => 'history',
			'gc' => array(
				'threshold' => 900,
				'probability' => 5
			)
		),
		'session' => array(
		),
		'gc' => array(
			'threshold' => 900,
			'probability' => 5
		)
	);
	// @formatter:on

	/**
	 * Prevent direct instantiation. If this function is overloaded don't forget to
	 * call parent::_construct() as needed!
	 */
	protected function __construct($history_id, array $config = array())
	{
		$this->_history_id = $history_id;
		if (method_exists('\Arr', 'merge_replace'))
		{
			$this->_config = \Arr::merge_replace(static::$_config_defaults, $config);
		}
		else
		{
			$this->_config = \Arr::merge(static::$_config_defaults, $config);
		}
	}
	
	/**
	 * Loads the GC if it exists
	 */
	protected function _load_gc(array $config = array())
	{
		// If exists then load the Garbage Collector for the driver and start it
		$gc = 'History_Driver_GC_' . ucwords($this->_config['name']);
		class_exists($gc) and $this->_gc = $gc::forge($this, $config);
		$this->_gc->start();
	}

	/**
	 * Forges a new History_Driver instance
	 *
	 * @return History_Driver
	 */
	public static function forge($history_id, array $config = array())
	{
		return new static($history_id, $config);
	}

	/**
	 * Gets the driver name (type)
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Loads the entries using the driver's options
	 *
	 * @return array
	 */
	abstract public function load();

	/**
	 * Saves the entries using the driver's options
	 *
	 * @return bool
	 */
	abstract public function save(array $entries);
}
