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

// @formatter:off
class History_Entry_Exception extends History_Exception {}
// @formatter:on

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
		'datetime' => null
	);
	// @formatter:on

	/**
	 * Prevent direct instantiation
	 */
	private function __construct(array $data = array())
	{
		if (method_exists('\Arr', 'merge_replace'))
		{
			$this->_data = \Arr::merge_replace(static::$_data_defaults, $data);
		}
		else
		{
			$this->_data = \Arr::merge(static::$_data_defaults, $data);
		}

		// TODO: When Fuel PHP v1.1 is released get rid of this fallback
		if (\Fuel::VERSION >= 1.1)
		{
			(is_null($this->_data['datetime']) or !($this->_data['datetime'] instanceof \Fuel\Core\Date)) and $this->_data['datetime'] = \Date::forge();
		}
		else
		{
			(is_null($this->_data['datetime']) or !($this->_data['datetime'] instanceof \Fuel\Core\Date)) and $this->_data['datetime'] = \Date::factory();
		}
	}

	/**
	 * Forges a new History_Entry object
	 *
	 * @return History_Entry
	 */
	public static function forge($data = '')
	{
		$options = array();

		if (is_string($data))
		{
			$uri = new \Uri($data);
			$options['uri'] = $uri->uri;
			$options['segments'] = $uri->segments;
		}
		else if (is_object($data) && $data instanceof \Uri)
		{
			$options['uri'] = $data->uri;
			$options['segments'] = $data->segments;
		}
		else if (is_array($data))
		{
			$options = $data;
			if ($options['uri'] != '' && empty($options['segments']))
			{
				$uri = new \Uri($options['uri']);
				$options['segments'] = $uri->segments;
			}
		}
		else
		{
			throw new History_Entry_Exception("Cannot forge a History_Entry object from the given parameter \$data.");
		}

		return new static($options);
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
		// TODO: When Fuel PHP v1.1 is released get rid of this fallback
		if (\Fuel::VERSION >= 1.1)
		{
			if (($value = \Arr::get($this->_data, $key, null)) === null)
			{
				throw new History_Entry_Exception("The property '{$key}' does not exist.");
			}
		}
		else
		{
			if (($value = \Arr::element($this->_data, $key, null)) === null)
			{
				throw new History_Entry_Exception("The property '{$key}' does not exist.");
			}
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
	 * Gets the controller part from the uri in the History_Entry object. Returns an
	 * empty string if none is found or the uri is empty. If using Routes this won't
	 * match the really executed controller as the History class records only the uri
	 * Requests.
	 *
	 * @return string The name of the controller part from the entry
	 */
	public function get_controller()
	{
		$segments = \Arr::get($this->_data, 'segments', array());

		return (isset($segments[0]) ? $segments[0] : '');
	}

	/**
	 * Gets the method part from the uri in the History_Entry object. Returns an
	 * empty string if none is found or the uri is empty or contains only the
	 * controller part. If using Routes this won't match the really executed method
	 * in the controller as the History class records only the uri Requests.
	 *
	 * @return string The name of the method part from the entry
	 */
	public function get_method()
	{
		$segments = \Arr::get($this->_data, 'segments', array());

		return (isset($segments[1]) ? $segments[1] : '');
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
		}
		elseif (is_object($compare))
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
