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
abstract class History_Driver_GC
{
	/**
	 * @var History_Driver Contains the parent driver of the Garbage Collector
	 */
	protected $_parent = null;

	/**
	 * @var array Contains the config for the GC
	 */
	protected $_config = array();

	/**
	 * @var array of global config defaults
	 */
	protected static $_config_defaults = array('threshold' => 900, 'probability' => 5);

	/**
	 * Prevent direct instantiation
	 */
	private function __construct(History_Driver $parent, array $config = array())
	{
		$this->_parent = $parent;
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
	 * Forges a new History_Driver_GC object
	 *
	 * @return History_Driver_GC
	 */
	public static function forge(History_Driver $parent, array $config = array())
	{
		return new static($parent, $config);
	}

	/**
	 * Starts the garbage collector. It determines if the collect method should be
	 * called based on the probability and some randomness
	 */
	public function start()
	{
		// By the law of probability we should now COLLECT the garbage!
		if (mt_rand(0, 100) < $this->_config['probability'])
		{
			$this->collect();
		}
	}

	/**
	 * Collects the garbage. This method is specific to driver used.
	 * 
	 * @return int Number of stacks deleted (files for File driver, rows for Database driver)
	 */
	abstract function collect();
}
