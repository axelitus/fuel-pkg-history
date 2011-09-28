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
class History_Driver_GC_File extends History_Driver_GC
{
	/**
	 * Collects the garbage for the History_Driver_File driver
	 */
	public function collect()
	{
		$path = $this->_parent->get_path();
		$prefix = $this->_parent->get_prefix();
		if ($handle = opendir($path))
		{
			$expire = \Date::forge()->get_timestamp() - $this->_config['threshold'];
			while (($file = readdir($handle)) !== false)
			{
				$fullpath = $path . DS . $file;
				if(filetype($fullpath) == 'file' && strpos($file, $prefix) === 0 && filemtime($fullpath) < $expire)
				{
					@unlink($fullpath);
				}
			}
			closedir($handle);
		}
	}

}
