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
class History_Driver_Session extends History_Driver
{
	/**
	 * Gets the driver name (type)
	 *
	 * @return string
	 */
	public function get_name()
	{
		return 'session';
	}
	
	/**
	 * Loads the entries using the driver's options
	 *
	 * @return array
	 */
	public function load()
	{
		$entries = array();

		$payload = \Session::get($this->_history_id, '');
		if ($payload != '')
		{
			// Uncompress the payload if needed
			$payload = $this->_uncompress($payload);
			
			// Decode the payload if needed
			$payload = $this->_decode($payload);
			
			// Unserialize payload and verify if the entries array is indeed an array else default to empty array
			is_array(($entries = unserialize($payload))) or $entries = array();
		}

		return $entries;
	}

	/**
	 * Saves the entries using the driver's options
	 *
	 * @return bool
	 */
	public function save(array $entries)
	{
		// Serialize the entries array
		$payload = serialize($entries);
		
		// Encode the payload if needed
		$payload = $this->_encode($payload);
		
		// Compress the payload if needed
		$payload = $this->_compress($payload);
		
		// Insert the payload into the Session
		$return = \Session::set($this->_history_id, $payload)->get($this->_history_id, null);
		$return = (($return === null)? false : true); 
		
		return $return;
	}
}