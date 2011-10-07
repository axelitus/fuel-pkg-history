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

// @formatter:off
class History_Driver_Database_Exception extends History_Exception {}
// @formatter:on

/**
 * History
 *
 * @package     Fuel
 * @subpackage  History
 */
class History_Driver_Database extends History_Driver
{
	/**
	 * @var string Contains the table to the stored entries
	 */
	protected $_table = '';

	/**
	 * @var string Contains the hash of stored record (it's just a random id)
	 */
	protected $_hash = '';
	
	/**
	 * @var array Contains the table structure to be used with the function \DBUtil::create_database()
	 */
	// @formatter:off
	protected $_table_fields = array(
		'hash' => array(
			'type' => 'varchar',
			'constraint' => 40			// 40 is the maximum length for hash (using sha1)
		),
		'content' => array(
			'type' => 'mediumtext',		// Can hold up to 16Mb of data, this should be more tha enough
			'null' => true
		),
		'updated' => array(
			'type' => 'datetime'
		) 
	);
	// @formatter:on

	/**
	 * Prevent direct instantiation. The parent's forge() method must be used to
	 * create an instance.
	 */
	protected function __construct($history_id, $config = array())
	{
		parent::__construct($history_id, $config);
		$driver = $this->get_name();

		// Verify if given table name exists
		$this->_table = $this->_config[$driver]['table'];
		if (!static::_table_exists($this->_table))
		{
			if($this->_config[$driver]['auto_create'])
			{
				\DBUtil::create_table($this->_table, $this->_table_fields, array('hash'));
				if(!static::_table_exists($this->_table))
				{
					throw new History_Driver_Database_Exception("Database table could not be created.");
				}
			}
			else
			{
				throw new History_Driver_Database_Exception("Database table does not exist.");
			}
		}

		if (!($this->_hash = \Session::get($this->_history_id . ".file", false)))
		{
			do
			{
				$this->_hash = static::_gen_hash();
			} while($this->_hash_exists($this->_hash));
			
			// TODO: When Fuel PHP v1.1 is released get rid of this fallback
			if(\Fuel::VERSION >= 1.1)
			{
				$now = \Date::forge();
			}
			else
			{
				$now = \Date::factory();
			}
			// @formatter:off
			list($insert_id, $rows_affected) = \DB::insert($this->_table)->set(array(
				'hash' => $this->_hash,
				'content' => null,
				'updated' => $now->format('%Y-%m-%d %H:%M:%S')
			))->execute();
			// @formatter:off
			
			if($rows_affected == 1)
			{
				\Session::set($this->_history_id . ".file", $this->_hash);
			}
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
		return 'database';
	}

	/**
	 * Gets the Table Name
	 *
	 * @return string
	 */
	public function get_table()
	{
		return $this->_table;
	}

	/**
	 * Gets the Record Hash
	 *
	 * @return string
	 */
	public function get_hash()
	{
		return $this->_hash;
	}
	
	protected static function _table_exists($table)
	{
		$return = false;
		$database = static::_get_database();
		
		$result = \DB::query("SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '{$database}' AND table_name = '{$table}';")->execute();
		if($result[0]['count'] == 1)
		{
			$return = true;
		}
		
		return $return;
	}
	
	protected static function _get_database()
	{
		\Config::load('db', true);
		$name = \Config::get('db.active');
		$config = \Config::get("db.{$name}");
		
		return $config['connection']['database'];
	}

	/**
	 * Verifies if hash already exists in the database
	 */
	protected function _hash_exists($hash)
	{
		$return = false;
		$result = \DB::select('hash')->from($this->_table)->where('hash', $hash)->limit(1)->execute();
		if (count($result) == 1)
		{
			$return = true;
		}

		return $return;
	}

	/**
	 * Loads the entries using the driver's options
	 *
	 * @return array
	 */
	public function load()
	{
		$entries = array();

		$result = \DB::select()->from($this->_table)->where('hash', $this->_hash)->limit(1)->execute();
		if (count($result) == 1)
		{
			$row = $result->as_array();
			$payload = $row[0]['content'];
			try
			{
				$entries = (($this->_config['secure']) ? unserialize(\Crypt::decode($payload)) : unserialize($payload));
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
			$entries = (is_array($entries)) ? $entries : array();
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
		$return = false;
		$payload = (($this->_config['secure']) ? \Crypt::encode(serialize($entries)) : serialize($entries));
		
		
		// TODO: When Fuel PHP v1.1 is released get rid of this fallback
		if(\Fuel::VERSION >= 1.1)
		{
			$now = \Date::forge();
		}
		else
		{
			$now = \Date::factory();
		}
		list($insert_id, $affected_rows) = \DB::query("INSERT INTO `{$this->_table}` (`hash`, `content`, `updated`) VALUES ('{$this->_hash}', '{$payload}', '{$now->format('%Y-%m-%d %H:%M:%S')}') ON DUPLICATE KEY UPDATE `content` = '{$payload}', `updated` = '{$now->format('%Y-%m-%d %H:%M:%S')}';")->execute();
		
		if($affected_rows == 1)
		{
			$return = true;
		}

		return $return;
	}

}
