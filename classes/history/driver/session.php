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

		// Process payload -> entries
		$entries = $this->_process_payload_to_entries($payload);

		return $entries;
	}

	/**
	 * Saves the entries using the driver's options
	 *
	 * @return bool
	 */
	public function save(array $entries)
	{
		// Process entries -> payload
		$payload = $this->_process_entries_to_payload($entries);

		// Insert the payload into the Session
		$return = \Session::set($this->_history_id, $payload)->get($this->_history_id, null);
		
		// Verify if the entries got inserted
		$return = (($return === null) ? false : true);

		return $return;
	}

}
