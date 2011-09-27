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
class Controller_History extends \Controller
{
	public function before()
	{
		// Automatically pushes the request to the History stack.
		History::push_request($this->request);
	}

	public function after()
	{
		// Automatically saves the History stack (using the loaded driver)
		// This is done here to allow History stack changes in the controller methods
		History::save();
	}

}
