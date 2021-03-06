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
 * Controller_History
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
class Controller_History extends \Controller
{
	public function before()
	{
		// Automatically pushes the request to the History stack.
		History::push_request($this->request);
	}

	public function after($response)
	{
		// Automatically saves the History stack (using the loaded driver)
		// This is done here to allow History stack changes in the controller methods
		History::save();

		// Respect the base Controller's return value
		return $response;
	}

}
