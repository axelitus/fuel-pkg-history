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
 * History_Driver
 *
 * @package     Fuel
 * @subpackage  History
 * @author      Axel Pardemann (http://github.com/axelitus)
 * @link        http://github.com/axelitus/fuel-pkg-history
 */
abstract class History_Driver
{
	/**
	 * @var string Contains the History's class history_id (the key in Session)
	 */
	protected $_history_id = 'history';

	/**
	 * @var array Contains the driver configuration
	 */
	protected $_config = array();

	/**
	 * @var History_Driver_GC Garbage Collector specific for driver if needed
	 */
	protected $_gc = null;

	// @formatter:off
	/**
	 * @var array of global config defaults
	 * Can be overloaded in derived class but PAY ATTENTION, otherwise something will
	 * break awfully. If this happens take look here first. These are the default
	 * values that should be present, unless your driver doesn't need them.
	 */
	protected static $_config_defaults = array(
		'name' => 'file',
		'compression' => array(
			'active' => true,
			'format' => 'zlib',
			'level' => 5
		),
		'secure' => true,
		'hash_length' => 8,
		'file' => array(
			'path' => '',
			'prefix' => 'his_',
			'extension' => 'tmp'
		),
		'database' => array(
			'table' => 'history',
			'auto_create' => true
		),
		'session' => array(
		),
		'gc' => array(
			'active' => true,
			'threshold' => 900,
			'probability' => 5
		)
	);
	// @formatter:on

	/**
	 * Prevent direct instantiation. If this function is overloaded don't forget to
	 * call parent::_construct() as needed!
	 */
	protected function __construct($history_id, array $config = array())
	{
		$this->_history_id = $history_id;
		$this->_config = \Arr::merge(static::$_config_defaults, $config);
		
		// The hash length must be between 1 and 40
		$this->_config['hash_length'] = (($this->_config['hash_length'] > 0 && $this->_config['hash_length'] < 41) ? $this->_config['hash_length'] : 8);
	}

	/**
	 * Loads the GC if it exists
	 */
	protected function _load_gc(array $config = array())
	{
		// Is Garbage Collector active?
		if ($config['active'])
		{
			// If exists then load the Garbage Collector for the driver and start it
			$gc = 'History_Driver_GC_' . ucwords($this->_config['name']);
			if (class_exists($gc))
			{
				$this->_gc = $gc::forge($this, $config);

				// Log Info
				\Log::info(get_called_class() . "::_load_gc() - The GC collector for the specified driver was loaded.");

				$this->_gc->start();
			}
		}
	}

	/**
	 * Forges a new History_Driver instance
	 *
	 * @return History_Driver
	 */
	public static function forge($history_id, array $config = array())
	{
		return new static($history_id, $config);
	}

	/**
	 * Generates a random hash
	 */
	protected function _gen_hash()
	{
		// Some random magic!
		$rand = mt_rand();

		// Generate the hash using the hash_lnegth config value
		$hash = substr(md5($rand), 0, $this->_config['hash_length']);

		return $hash;
	}

	/**
	 * Compresses the payload to be stored if configured to do so using the specified
	 * format and level
	 *
	 * @return string the compressed payload using the configured options or the
	 * unmodified payload string
	 */
	protected function _compress($payload)
	{
		$return = $payload;

		if ($this->_config['compression']['active'] === true)
		{
			try
			{
				switch(strtoupper($this->_config['compression']['format']))
				{
					case 'DEFLATE':
						$return = @gzdeflate($return, $this->_config['compression']['level']);
						break;
					/*
					 * Not yet supported as there is no gzdecode() PHP method as of yet
					 case 'GZIP':
					 $return = gzencode($return, $this->_config['compression']['level']);
					 break;
					 * */
					default:
					// This includes the ZLIB option
						$return = @gzcompress($return, $this->_config['compression']['level']);
						break;
				}
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
		}

		return $return;
	}

	/**
	 * Unompresses the given payload if configured to do so using the specified
	 * format and level
	 *
	 * @return string the uncompressed payload using the configured options or the
	 * unmodified payload string
	 */
	protected function _uncompress($payload)
	{
		$return = $payload;

		if ($this->_config['compression']['active'] === true)
		{
			try
			{
				switch(strtoupper($this->_config['compression']['format']))
				{
					case 'DEFLATE':
						$return = @gzinflate($return);
						break;
					/*
					 * Not yet supported as there is no gzdecode() PHP method as of yet
					 case 'GZIP':
					 $return = gzdecode($return);
					 break;
					 * */
					default:
					// This includes the ZLIB option
						$return = @gzuncompress($return);
						break;
				}
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
		}

		return $return;
	}

	/**
	 * Encodes the given payload if configured to so
	 *
	 * @return string The encoded payload using the \Crypt class or the unmodified
	 * payload string
	 */
	protected function _encode($payload)
	{
		$return = $payload;

		if ($this->_config['secure'])
		{
			try
			{
				$return = \Crypt::encode($return);
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
		}

		return $return;
	}

	/**
	 * Decodes the given payload if configured to so
	 *
	 * @return string The decoded payload using the \Crypt class or the unmodified
	 * payload string
	 */
	protected function _decode($payload)
	{
		$return = $payload;

		if ($this->_config['secure'])
		{
			try
			{
				$return = \Crypt::decode($return);
			}
			catch(Exception $e)
			{
				\Log::error($e->getMessage());
			}
		}

		return $return;
	}

	/**
	 * Processes the entries array to output a payload string.
	 * It does compression and encoding if needed.
	 *
	 * @return array of History_Entry objects
	 */
	protected function _process_entries_to_payload(array $entries, $use_base64_encoding = false)
	{
		$return = '';

		if (!empty($entries))
		{
			// Serialize the entries array
			$return = @serialize($entries);

			// Encode the payload if needed
			$return = $this->_encode($return);

			// Compress the payload if needed
			$return = $this->_compress($return);

			// Encode data usign base64
			if ($use_base64_encoding)
			{
				$return = base64_encode($return);
			}
		}

		return $return;
	}

	/**
	 * Processes the payload string to extract the entries array.
	 * It does uncompression and unencoding if needed.
	 *
	 * @return array of History_Entry objects
	 */
	protected function _process_payload_to_entries($payload, $use_base64_encoding = false)
	{
		$return = array();

		if ($payload != '')
		{
			// Decode data using base64
			if ($use_base64_encoding)
			{
				$payload = base64_decode($payload);
			}

			// Uncompress the payload if needed
			$payload = $this->_uncompress($payload);

			// Decode the payload if needed
			$payload = $this->_decode($payload);

			// Unserialize payload and verify if the entries array is indeed an array else
			// default to empty array
			is_array(($return = @unserialize($payload))) or $return = array();
		}

		return $return;
	}

	/**
	 * Gets the driver name (type)
	 *
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Loads the entries using the driver's options
	 *
	 * @return array
	 */
	abstract public function load();

	/**
	 * Saves the entries using the driver's options
	 *
	 * @return bool
	 */
	abstract public function save(array $entries);
}
