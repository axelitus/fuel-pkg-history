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
 * TODO: We need a Garbage Collector to delete the files that are not needed anymore.
 */
class History_Driver_File extends History_Driver
{
	protected $_filepath = '';
	protected $_filename = '';

	protected function __construct($history_id, $config = array())
	{
		parent::__construct($history_id, $config);

		// Verify that the given path exists, if not set the path to sys_get_temp_dir()
		$this->_filepath = $this->_config['path'];
		if (!is_dir($this->_filepath))
		{
			// Log only if a path was provided
			if ($this->_filepath != '')
				\Log::warning(get_called_class() . "::__construct() - The given path ('{$this->_config['path']}') does not exist. The default temp dir ('" . sys_get_temp_dir() . "') will be used instead.");
			$this->_filepath = sys_get_temp_dir();
		}
		$this->_filepath = realpath($this->_filepath);

		// Generate a temp file and store it's name in the session under the history_id
		// key
		if (!($this->_filename = \Session::get($this->_history_id . ".file", false)))
		{
			$tmpfile = tempnam($this->_filepath, 'history_');
			$this->_filename = trim(str_replace($this->_filepath, '', $tmpfile), '/\\');
			\Session::set($this->_history_id . ".file", $this->_filename);
		}
	}

	/**
	 * Gets the driver name (type)
	 * 
	 * @return string
	 */
	public function get_name()
	{
		return 'file';
	}

	/**
	 * Gets the File Path
	 * 
	 * @return string
	 */
	public function get_filepath()
	{
		return $this->_filepath;
	}

	/**
	 * Gets the File Name with extension included.
	 * 
	 * @return string
	 */
	public function get_filename()
	{
		return $this->_filename;
	}

	/**
	 * Gets the Full Path (File Path + File Name) with extension included.
	 * 
	 * @return string
	 */
	public function get_fullpath()
	{
		return $this->_filepath . DS . $this->_filename;
	}

	/**
	 * Loads the entries using the driver's options
	 *
	 * @return array
	 */
	public function load()
	{
		$entries = array();
		if (file_exists($this->get_fullpath()))
		{
			$payload = \File::read($this->get_fullpath(), true);
			$entries = (($this->_config['secure']) ? unserialize(\Crypt::decode($payload)) : unserialize($payload));
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
		return \File::update($this->_filepath, $this->_filename, $payload);
	}

}
