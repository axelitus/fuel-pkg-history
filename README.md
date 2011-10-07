# Fuel History package

The History package allows you to have a browsing history stack.
The class uses different drivers to store the history stack. The included drivers are: File (default), Database and Session.
The \Fuel\Session class is used for storing driver data (that we can retrieve between requests) regardless of the driver used.
The data (History entries stack) itself is stored depending on the driver you choose.

## About

* Latest Version: 1.0
* Release Date: 05/10/2011
* Author: Axel Pardemann ([http://axelitus.mx] (http://axelitus.mx))

## Requirements

* Fuel PHP Framework version 1.0.1 or later (v1.1 will be preferred when it gets released)
* MySql Database v5.0 or later (if using the database driver)

## Development Team

* Axel Pardemann - Lead Developer ([http://dev.blogs.axelitus.mx] (http://dev.blogs.axelitus.mx))

## Repository

You can find the GitHub Repository on (https://github.com/axelitus/fuel-pkg-history)

## Suggestions / Issues / Fixes

* For Issues you can use [GitHub's Issue Tracker](https://github.com/axelitus/fuel-pkg-history/issues)
* If you have suggestions you can send an email to dev [at] axelitus [dot] mx
* If you have any fixes or new features you'd like to share please send them as Pull Request on [GitHub] (https://github.com/axelitus/fuel-pkg-history/pulls)

## Installation

The package installation is very easy. You can choose one of two methods described here.

### Manual

Just download the source code located at [axelitus' FuelPHP History package at GitHub](https://github.com/axelitus/fuel-pkg-history) and place it in a folder named history inside the packages folder in FuelPHP.

Alternatively you can use git to clone the repository directly (this will make your life easier when updating the package):

```
git clone git@github.com:axelitus/fuel-pkg-history history
```

### Using Oil

This package follows standard installation rules, which can be found within the [FuelPHP Documentation for Packages] (http://fuelphp.com/docs/general/packages.html)
First you need to add axelitus' GitHub location to the package configuration file (package.php) located in fuel/core/config/ folder (you are encouraged to copy this file to your app/config folder and edit that file instead):

```
// config/package.php
return array(
    'sources' => array(
        'github.com/fuel-packages',
        'github.com/axelitus',			// ADD THIS LINE
    ),
);
```

Then just run the following command on the terminal from where the oil command is (this command uses the changes proposed in the pull request [45](https://github.com/fuel/oil/pull/45) in the [GitHub Oil Repo](https://github.com/fuel/oil). If you are not using this changes, you can ommit the 'folder=history' part from the following command and please make sure to rename the newly created 'pkg-history' folder to 'history' under the packages folder or the package won't work):

```
php oil package install pkg-history folder=history
```

This method will change when Fuel v1.1 is released.

## Usage

The first thing you should do is load the package. This can be achieved manually by calling:

```
\Fuel::add_package('history');
```

To load it automatically you should edit your App's config file (config.php). Look for the 'always_load' key and under 'packages' and set an entry with the 'history' string. It should look similar to this:

```
...
'always_load'	=> array(
	'packages'	=> array(
		'history'
	),
...
```

### Exending the History Controller

The easiest way to start using this package is to extend the Controller_History class. This will allow the extended Controller to generate the History stack (load) and save it automatically between requests.

To extend the controller just extend the base class like:

```
class MyNewController extends Controller_History
{
}
```

The Controller_History class uses a modified version of the before() and after() controller methods, so if you overload this methods inside your extended controller it won't work automatically unless you call the parent::before() and parent::after() statements inside your own methods.

Be sure to check the code in the base class and modify it as needed if you don't want to use the parent::before() and parent::after() calls or you need to do soemthing 'special'.

### Configuration

The configuration is done using the file history.php in the config directory (you are encourage to copy this file to your app/config folder and edit that file instead).

An example of the contents of a history.php config file:

```
// config/history.php
return array(
	'history_id' => 'history',
	'driver' => array(
		'name' => 'file',
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
		'prevent_refresh' => true
	)
);
```

The options in the config array do the following, most of them (if not all) are optional and will default to something:

#### history_id (type: string, default: 'history')

The history_id configuration value is the key to be used in the Session to store driver data.

#### driver (type: array)

The driver configuration value is the driver to be used and it's options.

Example:

```
'driver' => array(
	'name' => 'file',
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
```

##### name (type: string, default: 'file')

The driver to be used. It can be one of the following: file|database|session

##### secure (type: bool, default: true)

Whether to encode the entries (using Fuel\Crypt class) or not.

This setting will be ignored when using the Session driver as the \Session class encodes encodes all data automatically unless otherwise stated in the own Session's config.

##### hash_length (type: int, default: 8)

The length of the hash to be used to identify the stack (used for drivers to store the data and be able retrieve it later).

##### gc (type: array)

The Garbage Collector options for the specified driver (if the driver has one).

Example:

```
'gc' => array(
	'active' => true,
	'threshold' => 900,
	'probability' => 5
)
```

###### active (type: bool, default: true)

Whether to use Garbage Collection or not. You should leave this on to prevent data flooding and even collisions. When set to false the Garbage Collecto won't even be instantiated.

###### threshold (type: int, default: 900)

Seconds that will last the unmodified items (files or records depending on driver) before the garbage collector deletes them (The default of 900 seconds equals 15 minutes).

###### probability (type: int, default: 5)

The probability percentage (between 0 and 100) for garbage collection. (Setting this to 0 will have the same effect as to set the active option to false, but the GC will be instantiated).

#### entries (type: array)

The entries configuration value sets the options to how the history stack is managed.

Example:

```
'entries' => array(
	'limit' => 15,
	'prevent_refresh' => true,
	'use_full_post' => false
)
```

##### limit (type: int, default: 15 [use 0 for unlimited])

This value limits the entries that the stack will record. This works in the FIFO manner.

##### prevent_refresh (type: bool, default: true)

When set to true this option prevents duplicate entries by refresh. What it essentially does is that it does not allow an exact follow-up entry. If a refresh is detected the new entry will be discarded, thus not registering it completely.

### Driver specifics

This section holds some driver specific configurations and notes. To know what every specific driver option does please refer to the package config file ('history.php' under config folder).

#### File driver

The file driver uses a specific config key named 'file' with the following options:

```
'file' => array(
	'path' => 'path_to_store_files',
	'prefix' => 'perfix_to_use_for_files',
	'extension' => 'extension_to_use_for_files',
)
```

If something fails please verify the following:

* Make sure that the specified path is writeable

WARNING! (Please take a look at this or you could experience some problems):

* The File Garbage Collector will collect *ALL* files that meet the codnitions:
	- Filename starts with prefix 'prefix' and has extension 'extension'
	- File is expired using the 'threshold' value

So please make sure that you use a dedicated path for this, or the prefix is unique to the History stack to rpevent loss of other data.

#### Database driver

The table structure that the Database driver relies on is the following:

	CREATE TABLE `history` (
	  `hash` varchar(40) NOT NULL,
	  `content` mediumtext,
	  `updated` datetime NOT NULL,
	  PRIMARY KEY (`hash`)
	) DEFAULT CHARSET=utf8

The database driver uses a specific config key named 'database' with the following options:

```
'database' => array(
	'table' => 'name_of_table',
	'auto_create' => boolean
)
```

#### Session driver

The Session driver uses the \Session core class to store the entries stack. This driver is intended to be used with care as it relies on the capabilities of the underlying class. This means that it dependes on the chosen driver to handle the sessions across Fuel. You should be aware of how the driver handles data and what it's limits are. At the moment this driver does not have a specifics config key, but the key 'session' is reserved for the future development.

WARNING!:

* When using the 'cookie' driver for the session, the storage capacity is limited to 4kb (that means that the history stack and any other data you are storing in the session counts towards this limit). When this limit is reached you'll see this message: "The session data stored by the application in the cookie exceeds 4Kb. Select a different session storage driver". Use this with care as it may broke your app completely. You are encourage to use this combination with a small entries limit like 2 (current and previous only).

### Methods

The useful methods (and the ones that you will be using the most (if not the only ones) while using the package) are listed in the following sections.

#### History class

This is the main class which handles the History stack.

##### History::VERSION

Contains a string for the current version of the package.

##### History::push(History_Entry $entry)

_Description:_ Pushes a History_Entry to the History stack.
_Static:_ Yes
_Return:_ void

```
History::push($entry);
```

##### History::push_request(\Fuel\Core\Request $request)

_Description:_ Pushes a History_Entry based on a \Fuel\Core\Request to the History stack.
_Static:_ Yes
_Return:_ void

```
// From a Controller method
History::push_request($this->request);
```

##### History::pop()

_Description:_ Pops the top-most (current) History_Entry from the History stack. Note: this will shorten the entries by one element. Returns null if no entry is found in the stack.
_Static:_ Yes
_Return:_ null|History_Entry

Note: this will shorten the entries by one element.

```
$entry = History::pop();
```

##### History::get_entries()

_Description:_ Gets the entries as an array.
_Static:_ Yes
_Return:_ array of History_Entry objects

```
$entries = History::get_entries();
foreach($entries as $entry)
{
	// do something with each entry
}
```

##### History::get_entry($index)

_Description:_ Gets the entry at the specified index. Returns null if index is out of bounds.
_Static:_ Yes
_Return:_ null|History_Entry

```
$entry = History::get_entry();
if($entry !== null)
{
	// do something with the entry
}
```

##### History::count()

_Description:_ Gets the history entries count.
_Static:_ Yes
_Return:_ int

```
$count = History::count();
if($count > 10)
{
	// do something if the entries count is greater than 10
}
```

##### History::current()

_Description:_ Gets the current History entry. Returns null if the stack is empty.
_Static:_ Yes
_Return:_ null|History_Entry

```
$entry = History::current();
echo $entry->uri;
echo $entry->get_controller();
echo $entry->get_method();
```

##### History::previous()

_Description:_ Gets the previous History entry. Returns null if no previous entry s found in the stack.
_Static:_ Yes
_Return:_ null|History_Entry

```
$entry = History::previous();
echo $entry->uri;
echo $entry->get_controller();
echo $entry->get_method();
```

##### History::load()

_Description:_ Loads the stored entries to the History stack using the configured driver.
_Static:_ Yes
_Return:_ bool

```
History::load();
```

##### History::save()

_Description:_ Saves the History to a store using the configured driver.
_Static:_ Yes
_Return:_ bool

```
History::save();
```

#### History_Entry class

This class represents an entry in the History stack.

##### History_Entry::forge($data)

_Description:_ Forges a new History_Entry object. It can take an array of data, an \Uri object or an uri string.
_Static:_ Yes
_Return:_ History_Entry

```
$data = array(
	'uri' => '/welcome/index',
	'segments => array(
		0 => 'welcome',
		1 => 'index'
	),
	'datetime' => \Date::forge()
);
$entry = History_Entry::forge($data);
```

The only required data option is uri. (In fact, instead of an array one could use only the uri string. All other values would be automatically created).

##### History_Entry::from_request(\Fuel\Core\Request $request)

_Description:_ Forges a new History_Entry from a Fuel\Core\Request object.
_Static:_ Yes
_Return:_ History_Entry

```
// From a Controller method
$entry = History_Entry::from_request($this->request);
```

##### History_Entry->get_controller()

_Description:_ Gets the controller part from the uri in the History_Entry object. Returns an empty string if none is found or the uri is empty. If using Routes this won't match the really executed controller as the History class records only the uri Requests.
_Static:_ No
_Return:_ null|string

```
$entry = History_Entry::forge('welcome/index');
$controller = $entry->get_controller();
```

##### History_Entry->get_method()

_Description:_ Gets the method part from the uri in the History_Entry object. Returns an empty string if none is found or the uri is empty or contains only the controller part. If using Routes this won't match the really executed method in the controller as the History class records only the uri Requests.
_Static:_ No
_Return:_ null|string

```
$entry = History_Entry::forge('welcome/index');
$method = $entry->get_method();
```

##### History_Entry->get_segment($index)

_Description:_ Gets the zero-based uri's segment specified by given index. Returns null if index is out of bounds.
_Static:_ No
_Return:_ null|string

```
$entry = History_Entry::forge('welcome/display/file/531');
$type = $entry->get_segment(2);
$id = $entry->get_segment(3);
```

##### History_Entry->serialize()

_Description:_ Serializes the History_Entry object.
_Static:_ No
_Return:_

```
$entry = History_Entry::forge('/');
$serialized = $entry->serialize();
```

##### History_Entry->unserialize($str)

_Description:_ Unserializes the entries' string to the History_Entry object. All instance members will be overwritten.
_Static:_ No
_Return:_

```
$serialized_string = 'a:1:{i:0;C:21:"History\History_Entry":195:{a:3:{s:3:"uri";s:13:"welcome/index";s:8:"segments";a:2:{i:0;s:7:"welcome";i:1;s:5:"index";}s:8:"datetime";O:14:"Fuel\Core\Date":2:{s:12:" * timestamp";i:1317144358;s:11:" * timezone";s:3:"UTC";}}}}';
$entry = History_Entry::forge('')->unserialize($serialized_string);
```
Note: the serialized string it's just a close-to-real example

##### History_Entry::equals($compare, $use_post_hash = true)

_Description:_ Compares this History_Entry instance with another and determines if they are equal.
It can be compared to a string (uri), a \Fuel\Uri object or a History_entry object. The use_post_hash param indicates whether to use the post hash to do the comparison or not.
_Static:_ Yes
_Return:_

```
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
```

## Future development

The first version has the basic functionality one would expect. New features will be evaluated and added as soon as possible.

### Features for next Release 1.0.3

The features for the next version are the ones listed above. (If you have any suggestions feel free to send them using [GitHub](https://github.com/axelitus) or send an email to dev [at] axelitus [dot] mx)

Features:

* Allow compression using different libraries.

## Special Thanks

Firstly I would like to thank the [Fuel Development Team] (http://fuelphp.com/about) for their magnificent framework and spent time for making our lives easier. Great work, keep it up!

Special thanks for he ones that helped by commenting, discussing, suggesting, testing, brainstorming (if I missed someone please let me know, if you don't want to appear in this list also let me know):

* [ShonM] (https://github.com/ShonM)
* [rclanan] (https://github.com/rclanan)
* [canton7] (https://github.com/canton7)
* [FrenkyNet] (https://github.com/FrenkyNet)