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
class History_Driver_GC_Database extends History_Driver_GC
{
	/**
	 * Collects the garbage for the History_Driver_Database driver
	 */
	public function collect()
	{
		// TODO: When Fuel PHP v1.1 is released get rid of this fallback
		if(\Fuel::VERSION >= 1.1)
		{
			$expire = \Date::forge(\Date::forge()->get_timestamp() - $this->_config['threshold']);
		}
		else
		{
			$expire = \Date::factory(\Date::factory()->get_timestamp() - $this->_config['threshold']);
		}
		
		$rows_affected = \DB::delete($this->_parent->get_table())->where('updated', '<', $expire->format('%Y-%m-%d %H:%M:%S'))->execute();
		
		return $rows_affected;
	}

}
