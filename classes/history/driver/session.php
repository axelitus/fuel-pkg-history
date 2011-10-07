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
			try
			{
				$entries = (($this->_config['secure']) ? unserialize(\Crypt::decode($payload)) : unserialize($payload));
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
			$entries = (is_array($entries))? $entries : array();
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
		$payload = (($this->_config['secure']) ? \Crypt::encode(serialize($entries)) : serialize($entries));
		$return = \Session::set($this->_history_id, $payload)->get($this->_history_id, null);
		
		$return = (($return === null)? false : true); 
		
		return $return;
	}
}