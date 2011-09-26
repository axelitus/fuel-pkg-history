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
class History_Entry implements \Serializable
{
	/**
	 * @var array Contains the entry data
	 */
	protected $_data = array();
	
	/**
	 * @var array of data defaults
	 */
	protected static $_data_defaults = array(
		'uri'		=> '',
		'segments'	=> array(),
		/* TODO: Define if this is really needed. It don't think so
		'route'		=> array(
			'segments'		=> array(),
			'named_params'	=> array(),
			'method_params'	=> array(),
			'path'			=> '',
			'module'		=> '',
			'directory'		=> '',
			'controller'	=> '',
			'action'		=> '',
			'translation'	=> ''
		),
		 */
		'datetime'	=> null
	);

	/**
	 * Prevent direct instantiation
	 */
	private function __construct(array $data = array())
	{
		$this->_data = \Arr::merge_replace(static::$_data_defaults, $data);
		(is_null($this->_data['datetime']) or !($this->_data['datetime'] instanceof \Fuel\Core\Date)) and $this->_data['datetime'] = \Date::factory();
	}

	/**
	 * Forges a new History_Entry object
	 */
	public static function forge(array $data = array())
	{
		return new static($data);
	}

	/**
	 * Forges a new History_Entry from a Fuel\Core\Request object
	 */
	public static function from_request(\Fuel\Core\Request $request)
	{
		$data = array();
		$data['uri'] = $request->uri->get();
		$data['segments'] = $request->uri->get_segments();
		/* TODO: Define is this is really needed. I don't think so
		$data['route']['segments'] = $request->route->segments;
		$data['route']['named_params'] = $request->route->named_params;
		$data['route']['method_params'] = $request->route->method_params;
		$data['route']['path'] = $request->route->path;
		$data['route']['module'] = $request->route->module;
		$data['route']['directory'] = $request->route->directory;
		$data['route']['controller'] = $request->route->controller;
		$data['route']['action'] = $request->route->action;
		$data['route']['translation'] = $request->route->translation;
		*/
		
		$return = static::forge($data);

		return $return;
	}

	/**
	 * Gets the given object property
	 */
	public function __get($key)
	{
		/* TODO: As the object currently does not hold multi-level data we don't need this
		$arr_key = str_replace('_', '.', $key);
		 */
		if(($value = \Arr::get($this->_data, $arr_key, null)) === null)
		{
			throw new \Fuel_Exception("The property '{$key}' does not exist.");
		}
		
		return $value;
	}

	/**
	 * This function needs to be present and working for the serialization functionality
	 * but it should be avoided as the history entries should not be modified. You should treat
	 * the object's properties as read-only.
	 */
	public function __set($key, $value)
	{
		\Arr::replace_item($this->_data, $key, $value);
	}

	/**
	 * Serializes the History_Entry object
	 */
	public function serialize()
	{
		return serialize($this->_data);
	}

	/**
	 * Unserializes the string to the History_Entry object
	 * @return History
	 */
	public function unserialize($str)
	{
		$this->_data = unserialize($str);
	}
	
	/**
	 * Compares this History_Entry instance with another and determines if they are equal
	 * @return bool
	 */
	public function equals(History_Entry $compare)
	{
		if($this->uri != $compare->uri || $this->segments != $compare->segments)
		{
			return false;
		}
		
		return true;
	} 

}
