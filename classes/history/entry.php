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

	// @formatter:off
	/**
	 * @var array of data defaults
	 */
	protected static $_data_defaults = array(
		'uri' => '',
		'segments' => array(),
		/* TODO: Define if this is really needed. I don't think so
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
		 * */
		'datetime' => null
	);
	// @formatter:on

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
	 * 
	 * @return History_Entry
	 */
	public static function forge(array $data = array())
	{
		return new static($data);
	}

	/**
	 * Forges a new History_Entry from a Fuel\Core\Request object
	 * 
	 * @return History_Entry
	 */
	public static function from_request(\Fuel\Core\Request $request)
	{
		$data = array();
		$data['uri'] = $request->uri->get();
		$data['segments'] = $request->uri->get_segments();
		/* TODO: Define if this is really needed. I don't think so
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
	 * 
	 * @return mixed
	 */
	public function __get($key)
	{
		/* TODO: As the object currently does not hold multi-level data we don't need
		 * this
		 $arr_key = str_replace('_', '.', $key);
		 */
		if (($value = \Arr::get($this->_data, $key, null)) === null)
		{
			throw new \Fuel_Exception("The property '{$key}' does not exist.");
		}

		return $value;
	}

	/**
	 * This function needs to be present and working for the serialization
	 * functionality but it should be avoided as the history entries should not be
	 * modified. The object's properties should be treated as read-only.
	 */
	public function __set($key, $value)
	{
		\Arr::replace_item($this->_data, $key, $value);
	}

	/**
	 * Serializes the History_Entry object
	 * 
	 * @return string
	 */
	public function serialize()
	{
		return serialize($this->_data);
	}

	/**
	 * Unserializes the entries' string to the History_Entry object
	 * 
	 * @return History_Entry
	 */
	public function unserialize($str)
	{
		$this->_data = unserialize($str);
		
		return $this;
	}

	/**
	 * Compares this History_Entry instance with another and determines if they are
	 * equal. It can be compared to a string (uri), a \Fuel\Uri object or a
	 * History_entry object.
	 * 
	 * @return bool
	 */
	public function equals($compare)
	{
		$uri = '';
		$segments = array();

		// Define the comparison properties
		if (is_string($compare))
		{
			$uri = new \Uri($compare);
			$segments = $uri->segments;
			$uri = $uri->uri;
		} elseif (is_object($compare))
		{
			if ($compare instanceof \Uri || $compare instanceof History_Entry)
			{
				$uri = $compare->uri;
				$segments = $compare->segments;
			}
		}

		// Do the comparison
		if ($this->uri != $uri || $this->segments != $segments)
		{
			return false;
		}

		return true;
	}

}
