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


Autoloader::add_core_namespace('History');

Autoloader::add_classes(array(
	'History\\History'					=> __DIR__.'/classes/history.php',
	'History\\History_Entry'			=> __DIR__.'/classes/history/entry.php',
	'History\\History_Driver'			=> __DIR__.'/classes/history/driver.php',
	'History\\History_Driver_file'		=> __DIR__.'/classes/history/driver/file.php',
	'History\\History_Driver_database'	=> __DIR__.'/classes/history/driver/database.php',
	'History\\History_Driver_session'	=> __DIR__.'/classes/history/driver/session.php',
	'History\\Controller_History'		=> __DIR__.'/classes/controller/history.php',
));