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

/**
 * History_Driver_GC
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
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

	// @formatter:off
	/**
	 * @var array of global config defaults
	 */
	protected static $_config_defaults = array(
		'active' => true,
		'threshold' => 900,
		'probability' => 5
	);
	// @formatter:on
	/**
	 * Prevent direct instantiation
	 */
	private function __construct(History_Driver $parent, array $config = array())
	{
		$this->_parent = $parent;
		$this->_config = \Arr::merge(static::$_config_defaults, $config);
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
			$items_collected = $this->collect();
			
			// Log Info
			\Log::info(get_called_class() . "::start() - The Garbage Collector for the specified driver has collected {$items_collected} items.");
		}
	}

	/**
	 * Collects the garbage. This method is specific to driver used.
	 * 
	 * @return int Number of items deleted (files for File driver, rows for Database driver)
	 */
	abstract function collect();
}
