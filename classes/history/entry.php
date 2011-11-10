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
 * History_EntryException
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
class History_EntryException extends HistoryException {}
// @formatter:on

/**
 * History_Entry
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
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
		'referer' => '',
		'segments' => array(),
		'datetime' => null,
		'post' => array(
			'hash' => '',
			'data' => array()
		)
	);
	// @formatter:on

	/**
	 * Prevent direct instantiation
	 */
	private function __construct(array $data = array(), $use_full_post = false)
	{
		// Set the entry's data
		$this->_data = \Arr::merge(static::$_data_defaults, $data);

		// Set the current date if non is given
		(is_null($this->_data['datetime']) or !($this->_data['datetime'] instanceof \Fuel\Core\Date)) and $this->_data['datetime'] = \Date::forge();

		// Create the post hash to determine if it's the same uri but different post data
		if ($use_full_post)
		{
			$this->_data['post']['data'] = \Input::post();
		}
		
		// Get the post's hash
		$this->_data['post']['hash'] = sha1(json_encode(\Input::post()));
	}

	/**
	 * Forges a new History_Entry object
	 *
	 * @return History_Entry
	 */
	public static function forge($data = '', $use_full_post = false)
	{
		$options = array();

		if (is_string($data))
		{
			$uri = new \Uri($data);
			$options['uri'] = $uri->uri;
			$options['segments'] = $uri->segments;
			$options['referer'] = \Input::referrer(static::$_data_defaults['referer']);
		}
		else if (is_object($data) && $data instanceof \Uri)
		{
			$options['uri'] = $data->uri;
			$options['segments'] = $data->segments;
			$options['referer'] = \Input::referrer(static::$_data_defaults['referer']);
		}
		else if (is_array($data))
		{
			$options = $data;
			if ($options['uri'] != '' && empty($options['segments']))
			{
				$uri = new \Uri($options['uri']);
				$options['segments'] = $uri->segments;
			}

			if (!isset($_SERVER['HTTP_REFERER']))
			{
				$options['referer'] = \Input::referrer(static::$_data_defaults['referer']);
			}
		}
		else
		{
			throw new History_EntryException("Cannot forge a History_Entry object from the given parameter \$data.");
		}

		return new static($options, $use_full_post);
	}

	/**
	 * Forges a new History_Entry from a Fuel\Core\Request object
	 *
	 * @return History_Entry
	 */
	public static function from_request(\Fuel\Core\Request $request, $use_full_post = false)
	{
		$data = array();
		$data['uri'] = $request->uri->get();
		$data['segments'] = $request->uri->get_segments();

		$return = static::forge($data, $use_full_post);

		return $return;
	}

	/**
	 * Gets the given object property
	 *
	 * @return mixed
	 */
	public function __get($key)
	{
		// Get the attribute if exists
		if (($value = \Arr::get($this->_data, $key, null)) === null)
		{
			throw new History_EntryException("The property '{$key}' does not exist.");
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
	 * Gets the controller part from the uri in the History_Entry object. Returns
	 * null if none is found or the uri is empty. If using Routes this won't match
	 * the really executed controller as the History class records only the uri
	 * Requests.
	 *
	 * @return null|string The name of the controller part from the entry
	 */
	public function get_controller()
	{
		// Controller is segment 1 (one-based index)
		return $this->get_segment(1);
	}

	/**
	 * Gets the method part from the uri in the History_Entry object. Returns null if
	 * none is found or the uri is empty or contains only the controller part. If
	 * using Routes this won't match the really executed method in the controller as
	 * the History class records only the uri Requests.
	 *
	 * @return null|string The name of the method part from the entry
	 */
	public function get_method()
	{
		// Method is segment 2 (one-based index)
		return $this->get_segment(2);
	}

	/**
	 * Gets the one-based uri's segment specified by given index. Returns null if
	 * index is out of bounds.
	 * 
	 * Segment index is 1 based, not 0 based to match \Fuel\Uri::get_segment() method.
	 *
	 * @return null|string The uri segment or null if it does not exists
	 */
	public function get_segment($index)
	{
		// Get the segments
		$segments = \Arr::get($this->_data, 'segments', array());

		return \Arr::get($segments, $index - 1, null);
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
	 * History_entry object. The use_post_hash param indicates whether to use the
	 * post hash to do the comparison or not.
	 *
	 * @return bool
	 */
	public function equals($compare, $use_post_hash = true)
	{
		$uri = '';
		$segments = array();
		// @formatter:off
		$post = array(
			'hash' => '',
			'data' => array()
		);
		// @formatter:on

		// Define the comparison properties
		if (is_string($compare))
		{
			$uri = new \Uri($compare);
			$segments = $uri->segments;
			$uri = $uri->uri;
		}
		elseif (is_object($compare))
		{
			if ($compare instanceof \Uri)
			{
				$uri = $compare->uri;
				$segments = $compare->segments;
			}
			elseif ($compare instanceof History_Entry)
			{
				$uri = $compare->uri;
				$segments = $compare->segments;
				$post_hash = $compare->post['hash'];
			}
		}

		// Do the comparison
		if ($this->uri != $uri || $this->segments != $segments)
		{
			return false;
		}

		// Include post hash in the comparison
		if ($use_post_hash && $this->post['hash'] != $post_hash)
		{
			return false;
		}

		return true;
	}

}
