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

/**
 * History Autoloader
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
Autoloader::add_namespace('History', __DIR__.'/classes/');

Autoloader::add_core_namespace('History');

Autoloader::add_classes(array(
	'History\\History'								=> __DIR__.'/classes/history.php',
	'History\\HistoryException'						=> __DIR__.'/classes/history.php',
	'History\\History_Entry'						=> __DIR__.'/classes/history/entry.php',
	'History\\History_EntryException'				=> __DIR__.'/classes/history/entry.php',
	'History\\History_Driver'						=> __DIR__.'/classes/history/driver.php',
	'History\\History_Driver_File'					=> __DIR__.'/classes/history/driver/file.php',
	'History\\History_Driver_Database'				=> __DIR__.'/classes/history/driver/database.php',
	'History\\History_Driver_Database_Exception'	=> __DIR__.'/classes/history/driver/database.php',
	'History\\History_Driver_Session'				=> __DIR__.'/classes/history/driver/session.php',
	'History\\History_Driver_GC'					=> __DIR__.'/classes/history/driver/gc.php',
	'History\\History_Driver_GC_File'				=> __DIR__.'/classes/history/driver/gc/file.php',
	'History\\History_Driver_GC_Database'			=> __DIR__.'/classes/history/driver/gc/database.php',
	'History\\Controller_History'					=> __DIR__.'/classes/controller/history.php',
));