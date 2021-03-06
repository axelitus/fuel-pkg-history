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
 * History_Driver_GC_File
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
class History_Driver_GC_File extends History_Driver_GC
{
	/**
	 * Collects the garbage for the History_Driver_File driver
	 */
	public function collect()
	{
		$return = 0;
		
		$path = $this->_parent->get_path();
		$prefix = $this->_parent->get_prefix();
		$extension = $this->_parent->get_extension();
		if ($handle = opendir($path))
		{
			// Calculate the expiration difference
			$expire = \Date::forge()->get_timestamp() - $this->_config['threshold'];
			
			while (($file = readdir($handle)) !== false)
			{
				$fullpath = $path . DS . $file;
				if(filetype($fullpath) == 'file' && strpos($file, $prefix) === 0 && substr($file, -strlen($extension)) === $extension &&  filemtime($fullpath) < $expire)
				{
					if(@unlink($fullpath))
					{
						$return++;
					}
				}
			}
			closedir($handle);
		}
		
		return $return;
	}

}
