# FuelPHP History package

The History package allows you to have a browsing history stack.
The class uses different drivers to store the history stack. The included drivers are: File (default), Database and Session.
The `Fuel\Session` class is used for storing driver data (that we can retrieve between requests) regardless of the driver used.
The data (History entries stack) itself is stored depending on the driver you choose.

## About

* Latest Version: v1.1
* Released: 10/11/2011
* Author: Axel Pardemann ([http://axelitus.mx](http://axelitus.mx))

## Requirements

* FuelPHP Framework version v1.1 (it is not backwards compatible anymore, please update you FuelPHP version).
* MySql Database v5.0 or later (required only if using the database driver). Other databases may work but they are not tested.

## Development Team

* Axel Pardemann - Lead Developer ([http://dev.blogs.axelitus.mx](http://dev.blogs.axelitus.mx))

## Repository

You can find the GitHub Repository on [https://github.com/axelitus/fuel-pkg-history](https://github.com/axelitus/fuel-pkg-history)

## Suggestions / Issues / Fixes

* For Issues you can use [GitHub's Issue Tracker](https://github.com/axelitus/fuel-pkg-history/issues)
* If you have suggestions you can send an email to dev [at] axelitus [dot] mx
* If you have any fixes or new features you'd like to share please send them as Pull Requests on [GitHub](https://github.com/axelitus/fuel-pkg-history/pulls)

## Installation

The package installation is very easy. You can choose one of two methods described here.

### Manual

Just download the source code located at [axelitus' FuelPHP History package at GitHub](https://github.com/axelitus/fuel-pkg-history) and place it in a folder named `history` inside the packages folder in FuelPHP.

Alternatively you can use git to clone the repository directly (this will make your life easier when updating the package):

	git clone git@github.com:axelitus/fuel-pkg-history history


### Using Oil

Waiting for release v1.1 to complete this...

## Usage

The first thing you should do is load the package. This can be achieved manually by calling:

	\Package::load('history');

To load it automatically you should edit your app's config file (`config.php`). Look for the `always_load` key and under `packages` and set an entry with the 'history' string. It should look similar to this:

	...
	'always_load'	=> array(
		'packages'	=> array(
			'history'
		),
	...

### Exending the History Controller

The easiest way to start using this package is to extend the Controller_History class. This will allow the extended Controller to generate the History stack (load) and save it automatically between requests.

To extend the controller just extend the base class like:

	class MyNewController extends Controller_History
	{
	}

The Controller_History class uses a modified version of the `before()` and `after()` controller methods, so if you overload this methods inside your extended controller it won't work automatically unless you call the `parent::before()` and `parent::after()` statements inside your own methods.

Be sure to check the code in the base class and modify it as needed if you don't want to use the `parent::before()` and `parent::after()` calls or you need to do something 'special'.

### Configuration

The configuration is done using the file `history.php` in the config directory (you are encouraged to copy this file to your `app/config` folder and edit that file instead).

An example of the contents of a `history.php` config file:

	// config/history.php
	return array(
		'history_id' => 'history',
		'driver' => array(
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
				'extension' => '.tmp',
			),
			'gc' => array(
				'active' => true,
				'threshold' => 900,
				'probability' => 5
			)
		),
		'entries' => array(
			'limit' => 15,
			'prevent_refresh' => true,
			'use_full_post' => false,
			'exclude' => array(
				'_root_',
				'_404_',
				'welcome/test'
			)
		)
	);

The options in the config array do the following, most of them are optional and will default to something (they are marked with a default value):

#### history_id (type: string, default: 'history')

The `history_id` configuration value is the key to be used in the Session to store driver data.

#### driver (type: array)

The driver configuration value is the driver to be used and it's options.

Example:

	'driver' => array(
		'name' => 'file',
		'compression' => array(
			'active' => true,
			'format' => 'zlib',
			'level' => 5
		),
		'secure' => true,
		'hash_length' => 8,
		'[driver_specifics]' => array(
			...
		),
		'gc' => array(
			'active' => true,
			'threshold' => 900,
			'probability' => 5
		)
	)

##### name (type: string, default: 'file') [Options: file|database|session]

The driver to be used (one of three possible values).

##### compression (type: array)

The compression options to be used.

Example:

	'compression' => array(
		'active' => true,
		'format' => 'zlib',
		'level' => 5
	)

###### active (type: bool, default: true)

Whether to use compression or not.

###### format (type: string, default: 'zlib') [Options: zlib|deflate]

Whether to compress the data before encoding or not.

###### level (type: int, default: 5) [From 0 to 9]

The level of compression to use.

##### secure (type: bool, default: true)

Whether to encode the entries (using `Fuel\Crypt` class) or not.

This setting will be ignored when using the Session driver as the `Fuel\Session` class encodes all data automatically unless otherwise stated in the own Session's config.

##### hash_length (type: int, default: 8)

The length of the hash to be used to identify the stack (used for drivers to store the data and be able to retrieve it later).

##### gc (type: array)

The Garbage Collector options for the specified driver (if the driver has one).

Example:

	'gc' => array(
		'active' => true,
		'threshold' => 900,
		'probability' => 5
	)

###### active (type: bool, default: true)

Whether to use garbage collection or not. You should leave this set to `true` to prevent data flooding and even collisions. When set to `false` the Garbage Collector won't even be instantiated.

###### threshold (type: int, default: 900)

Seconds that will last the unmodified items (files or records depending on driver) before the garbage collector deletes them (The default of 900 seconds equals 15 minutes).

###### probability (type: int, default: 5)

The probability percentage (between 0 and 100) for garbage collection. (Setting this to 0 will have the same effect as to set the active option to false, but the GC will be instantiated).

#### entries (type: array)

The entries configuration value sets the options to how the history stack is managed.

Example:

	'entries' => array(
		'limit' => 15,
		'prevent_refresh' => true,
		'use_full_post' => false,
		'exclude' => array(
			'_root_',
			'_404_',
			'welcome/test'
		)
	)

##### limit (type: int, default: 15 [use 0 for unlimited])

This value limits the entries that the stack will register. The entries array works as a FIFO stack.

##### prevent_refresh (type: bool, default: true)

When set to `true` this option prevents duplicate entries by refresh. What it essentially does is that it does not allow an identical follow-up entry. If a refresh is detected the new entry will be discarded, thus not registering it completely.

##### use\_full\_post (type: bool, default: false)

Whether to save the full post data in the entry or just the post data hash.

##### exclude (type: array, default: array())

An array of uri's to be excluded from history registration (filtered). The special routes \_root\_ and \_404\_ can be used in this array, they'll get automatically converted to their actual value (from the `routes.php` config file).

* For the \_404\_ special route the value in the routes config file will be used.
* For the \_root\_ special route the value in the routes config file will be used and another empty item will be added (as it is root). Also a controller-only uri will be added if the 2nd segment of the route's config is 'index').

### Driver specifics

This section holds some driver specific configurations and notes. To know what every specific driver option does please refer to the package config file (`history.php` under config folder).

#### File driver

The file driver uses a specific config key named 'file' with the following options:

	'file' => array(
		'path' => 'path_to_store_files',
		'prefix' => 'perfix_to_use_for_files',
		'extension' => 'extension_to_use_for_files',
	)

If something fails please verify the following:

* Make sure that the specified path is writeable.

**WARNING!** (Please beware of this as it could generate some problems):

* The File Garbage Collector will collect **ALL** files that meet the following conditions:
	- Filename starts with prefix 'prefix' and has extension 'extension'.
	- File has already expired using the 'threshold' value.

**Note:** Please make sure that you use a dedicated path for storing the History stack or that the prefix is unique to all files to prevent loss of other data if you share the storing folder.

#### Database driver

The table structure that the Database driver relies on is the following (MySql 'specific' creation code, it may work with other DB drivers though):

	CREATE TABLE `history` (
	  `hash` varchar(40) NOT NULL,
	  `content` mediumtext,
	  `updated` datetime NOT NULL,
	  PRIMARY KEY (`hash`)
	) DEFAULT CHARSET=utf8

The database driver uses a specific config key named 'database' with the following options:

	'database' => array(
		'table' => 'name_of_table',
		'auto_create' => boolean
	)

#### Session driver

The Session driver uses the `Fuel\Session` core class to store the entries stack. This driver is intended to be used with care as it relies on the capabilities of the underlying class. This means that it dependes on the chosen driver to handle the sessions across FuelPHP. You should be aware of how the driver handles data and what it's limits are. At the moment, this driver does not have a specifics config key, but the key 'session' is reserved for possible future development.

**WARNING!:**

* When using the 'cookie' driver for the session, the storage capacity is limited to 4kb (that means that the history stack and any other data you are storing in the session counts towards this limit). When this limit is reached you'll see this message:

	> The session data stored by the application in the cookie exceeds 4Kb. Select a different session storage driver.

	Use this with care as it may broke your app completely. You are encouraged to use this driver with a small entries limit like 2 (current and previous only).

### Methods

The useful methods (and the ones that you will be using the most while using this package) are listed in the following sections.

#### History class

This is the main class which handles the History stack.

##### History::VERSION

Contains a string for the current version of the package.

##### History::push(History_Entry $entry)

**Description:** Pushes a `History_Entry` to the History stack.  
**Static:** Yes  
**Return:** `void`

	History::push($entry);

##### History::push_request(Fuel\Core\Request $request)

**Description:** Pushes a `History_Entry` based on a `Fuel\Core\Request` to the History stack.  
**Static:** Yes  
**Return:** `void`

	// From within a Controller method
	History::push_request($this->request);

##### History::pop()

**Description:** Pops the top-most (current) `History_Entry` from the History stack. Note: this will shorten the entries by one element. Returns `null` if the stack is empty.
**Static:** Yes  
**Return:** `null|History_Entry`

Note: this will shorten the entries by one element.

	$entry = History::pop();

##### History::get_entries()

**Description:** Gets the entries as an array.  
**Static:** Yes  
**Return:** `array` (of `History_Entry` objects)

	$entries = History::get_entries();
	foreach($entries as $entry)
	{
		// do something with each entry
	}

##### History::get_entry($index)

**Description:** Gets the entry at the specified index. Returns `null` if index is out of bounds.  
**Static:** Yes  
**Return:** `null|History_Entry`

	$entry = History::get_entry();
	if($entry !== null)
	{
		// do something with the entry
	}

##### History::count()

**Description:** Gets the history entries count.  
**Static:** Yes  
**Return:** `int`

	$count = History::count();
	if($count > 10)
	{
		// do something if the entries count is greater than 10
	}

##### History::current()

**Description:** Gets the current History entry. Returns `null` if the stack is empty.  
**Static:** Yes  
**Return:** `null|History_Entry`

	$entry = History::current();
	echo $entry->uri;
	echo $entry->get_controller();
	echo $entry->get_method();

##### History::previous()

**Description:** Gets the previous History entry. Returns `null` if no previous entry is found in the stack.  
**Static:** Yes  
**Return:** `null|History_Entry`

	$entry = History::previous();
	echo $entry->uri;
	echo $entry->get_controller();
	echo $entry->get_method();

##### History::load()

**Description:** Loads the stored entries to the History stack using the configured driver.  
**Static:** Yes  
**Return:** `bool`

	History::load();

##### History::save()

**Description:** Saves the History to the _store_ using the configured driver.  
**Static:** Yes  
**Return:** `bool`

	History::save();

#### History_Entry class

This class represents an entry in the History stack.

##### History_Entry::forge($data)

**Description:** Forges a new `History_Entry` object. It can take an array of data, a `Fuel\Uri` object or an uri string.  
**Static:** Yes  
**Return:** `History_Entry`

	$data = array(
		'uri' => '/welcome/index',
		'segments => array(
			0 => 'welcome',
			1 => 'index'
		),
		'datetime' => \Date::forge()
	);
	$entry = History_Entry::forge($data);

The only required data option is uri. (In fact, instead of an array one could use only the uri string. All other values would be automatically created).

##### History\_Entry::from_request(Fuel\Core\Request $request)

**Description:** Forges a new `History_Entry` from a `Fuel\Core\Request` object.  
**Static:** Yes  
**Return:** `History_Entry`

	// From within a Controller method
	$entry = History_Entry::from_request($this->request);

##### History\_Entry->get_controller()

**Description:** Gets the controller part from the uri in the `History_Entry` object. Returns an empty string if none is found or the uri is empty. If using routes this won't match the really executed controller as the History class records only the uri requests.
**Static:** No  
**Return:** `null|string`

	$entry = History_Entry::forge('welcome/index');
	$controller = $entry->get_controller();

##### History\_Entry->get_method()

**Description:** Gets the method part from the uri in the `History_Entry` object. Returns an empty string if none is found or the uri is empty or contains only the controller part. If using routes this won't match the really executed method in the controller as the History class records only the uri requests.  
**Static:** No  
**Return:** `null|string`

	$entry = History_Entry::forge('welcome/index');
	$method = $entry->get_method();

##### History\_Entry->get_segment($index)

**Description:** Gets the one-based uri's segment specified by given index. Returns null if index is out of bounds.  
**Static:** No  
**Return:** `null|string`

	$entry = History_Entry::forge('welcome/display/file/531');
	$type = $entry->get_segment(2);
	$id = $entry->get_segment(3);

##### History_Entry->serialize()

**Description:** Serializes the `History_Entry` object.  
**Static:** No  
**Return:** `string`

	$entry = History_Entry::forge('/');
	$serialized = $entry->serialize();

##### History_Entry->unserialize($str)

**Description:** Unserializes the entries' string to the `History_Entry` object. All instance members will be overwritten.  
**Static:** No  
**Return:** `History_Entry`

	$serialized_string = 'a:1:{i:0;C:21:"History\History_Entry":195:{a:3:{s:3:"uri";s:13:"welcome/index";s:8:"segments";a:2:{i:0;s:7:"welcome";i:1;s:5:"index";}s:8:"datetime";O:14:"Fuel\Core\Date":2:{s:12:" * timestamp";i:1317144358;s:11:" * timezone";s:3:"UTC";}}}}';
	$entry = History_Entry::forge('')->unserialize($serialized_string);

Note: the serialized string is a close-to-real example.

##### History\_Entry::equals($compare, $use\_post\_hash = true)

**Description:** Compares this `History_Entry` instance with another and determines if they are equal.
It can be compared to a string (uri), a `Fuel\Uri` object or a `History_Entry` object. The `use_post_hash` param indicates whether to use the post hash to do the comparison or not.  
**Static:** Yes  
**Return:** `bool`

	$uri = new \Uri('/');
	$entry = History_Entry::forge($uri);
	$compare = History_Entry::forge('welcome/index');
	
	if($entry->equals($compare))
	{
		// do something if entries are equal
	}
	
	if($entry->equals($uri))
	{
		// do something if entry equals $uri object
	}
	
	if($entry->equals($uri->uri))
	{
		// do something if entry equals $uri's object uri
	}

## Future development

The basic functionality one would expect is already covered. New features will be evaluated and added as soon as possible. Please feel free to send feature requests through the [Github repository](https://github.com/axelitus/fuel-pkg-history).

## Special Thanks

Firstly I would like to thank the [Fuel Development Team](http://fuelphp.com/about) for their magnificent framework and spent time for making our lives easier. Great work, keep it up!

Special thanks for he ones that helped by commenting, discussing, suggesting, testing, brainstorming (if I missed someone please let me know, if you don't want to appear in this list also let me know):

* [ShonM](https://github.com/ShonM)
* [rclanan](https://github.com/rclanan)
* [canton7](https://github.com/canton7)
* [FrenkyNet](https://github.com/FrenkyNet)