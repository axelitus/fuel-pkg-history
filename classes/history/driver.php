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
	 * @var array of global config defaults
	 * Can be overloaded in derived class but PAY ATTENTION
	 */
	protected static $_config_defaults = array(
		'path' => '',
		'secure' => true
	);
	
	/**
	 * Prevent direct instantiation
	 * If this function is overloaded don't forget to call parent::_construct() as needed!
	 */
	protected function __construct($history_id, array $config = array())
	{
		$this->_history_id = $history_id;
		$this->_config = \Arr::merge_replace(static::$_config_defaults, $config);
	}
	
	public static function forge($history_id, array $config = array())
	{
		return new static($history_id, $config); 
	}

	/**
	 * Loads the entries using the driver's options
	 * @return array 
	 */
	abstract public function load();
	
	/**
	 * Saves the entries using the driver's options
	 */
	abstract public function save(array $entries);
}
