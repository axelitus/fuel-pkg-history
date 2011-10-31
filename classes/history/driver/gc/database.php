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
class History_Driver_GC_Database extends History_Driver_GC
{
	/**
	 * Collects the garbage for the History_Driver_Database driver
	 */
	public function collect()
	{
		// Calculate the expiration difference
		$expire = \Date::forge(\Date::forge()->get_timestamp() - $this->_config['threshold']);
		
		$rows_affected = \DB::delete($this->_parent->get_table())->where('updated', '<', $expire->format('%Y-%m-%d %H:%M:%S'))->execute();
		
		return $rows_affected;
	}

}
