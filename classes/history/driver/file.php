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
class History_Driver_File extends History_Driver
{
	/**
	 * @var string Contains the path to the stored files
	 */
	protected $_path = '';

	/**
	 * @var string Contains the file prefix
	 */
	protected $_prefix = '';

	/**
	 * @var string Contains the extension of the stored files
	 */
	protected $_extension = '';

	/**
	 * @var string Contains the hash of stored file (it's just a random id)
	 */
	protected $_hash = '';

	/**
	 * Prevent direct instantiation. The parent's forge() method must be used to
	 * create an instance.
	 */
	protected function __construct($history_id, $config = array())
	{
		parent::__construct($history_id, $config);
		$driver = $this->get_name();

		// Verify that the given path exists, if not set the path to sys_get_temp_dir()
		$this->_path = $this->_config[$driver]['path'];
		if (!is_dir($this->_path))
		{
			// Log only if a path was provided
			if ($this->_path != '')
			{
				\Log::warning(get_called_class() . "::__construct() - The given path ('{$this->_config['path']}') does not exist. The default temp dir ('" . sys_get_temp_dir() . "') will be used instead.");
			}
			$this->_path = sys_get_temp_dir();
		}

		// Get the real path and trim any trailing slashes
		$this->_path = rtrim(realpath($this->_path), '/\\');

		// Get the prefix
		$this->_prefix = $this->_config[$driver]['prefix'];

		// Get the extension
		$this->_extension = $this->_config[$driver]['extension'];

		if (!($this->_hash = \Session::get($this->_history_id . ".file", false)))
		{
			do
			{
				$this->_hash = static::_gen_hash();
			} while(file_exists($this->get_fullpath()));
			\File::create($this->_path, $this->get_filename());
			\Session::set($this->_history_id . ".file", $this->_hash);
		}

		// For this driver we'll try to load the GC
		$this->_load_gc($config['gc']);
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
	public function get_path()
	{
		return $this->_path;
	}

	/**
	 * Gets the File Prefix
	 *
	 * @return string
	 */
	public function get_prefix()
	{
		return $this->_prefix;
	}

	/**
	 * Gets the File Extension
	 *
	 * @return string
	 */
	public function get_extension()
	{
		return $this->_extension;
	}

	/**
	 * Gets the File Hash
	 *
	 * @return string
	 */
	public function get_hash()
	{
		return $this->_hash;
	}

	/**
	 * Gets the File Name (File Prefix + File Hash + File Extension)
	 *
	 * @return string
	 */
	public function get_filename()
	{
		return $this->_prefix . $this->_hash . $this->_extension;
	}

	/**
	 * Gets the Full Path (File Path + DS + File Prefix + File Hash + File Extension)
	 *
	 * @return string
	 */
	public function get_fullpath()
	{
		return $this->_path . DS . $this->get_filename();
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

			// Process payload -> entries
			$entries = $this->_process_payload_to_entries($payload);
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
		// Process entries -> payload
		$payload = $this->_process_entries_to_payload($entries);
		
		// Log Info
		\Log::info(get_called_class() . "::save() - The file to be saved is: {$this->get_fullpath()}");

		return \File::update($this->_path, $this->get_filename(), $payload);
	}

}
