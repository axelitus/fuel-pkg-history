# Fuel History package

The History package allows you to have a browsing history stack.
The class uses different drivers to store the history stack. The included drivers are: File (default), Database and Session.
The \Fuel\Session class is used for storing driver data (that we can retrieve between requests) regardless of the driver used.
The data (History entries stack) itself is stored depending on the driver you choose.

## About

* Version: 1.0
* Author: Axel Pardemann ([http://axelitus.mx] (http://axelitus.mx))

## Development Team

* Axel Pardemann - Lead Developer ([http://dev.blogs.axelitus.mx] (http://dev.blogs.axelitus.mx))

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
First you need to add axelitus' GitHub location to the package configuration file (package.php) located in fuel/core/config/ folder (you are encourage to copy this file to your app/config folder and edit that file instead):

```
// config/package.php
return array(
    'sources' => array(
        'github.com/fuel-packages',
        'github.com/axelitus', // ADD THIS LINE
    ),
);
```

Then just run the following command from the terminal while in your applications' base directory

```
oil package install pkg-history
```

## Usage

### Exending the History Controller

The easiest way to start using this package is to extend the Controller_History class. This will allow the extended Controller to automatically generate the History stack and save it between requests.

To extend the controller just extend the base class like:

```
class MyNewController extends Controller_History
{
}
```

The Controller_History class uses the before() and after() controller functions, so if you overload this in your derived controller it won't work unless you use the parent::before() and parent::after() statements inside your functions.
Check the code in the base class and modify it as needed if you don't want to use the parent::before() and parent::after() calls.

### Configuration

The configuration is done using the file history.php in the config directory (you are encourage to copy this file to your app/config folder and edit that file instead).

Config example:

```
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

#### history_id (type: string, default: 'history')

The history_id configuration value is the key to be used in the Session to store driver data.

#### driver (type: array)

The driver configuration value is the driver to be used and it's options.

##### name (type: string, default: 'file')

The driver to be used. It can be one of the following: file|database|session

##### path (type: string, default: '')

Using file driver: The path to where the files will be saved. If the path does not exist or is not specified, the default sys temp path will be used.

Using database driver: The table name to where the files will be saved. If the table does not exist, an Exception will be thrown.

Using session driver: This will be ignored.

##### secure (type: bool, default: true)

Using file and database driver: The stored data will be encoded using the \Crypt class.

Using session driver: This will be ignored. The session automatically encodes data unless otherwise stated in the Session's config.

#### entries (type: array)

The entries configuration value sets the options to how the history stack is managed.

##### limit (type: int, default: 15 [use 0 for unlimited])

This value limits the entries that the stack will record. This works in the FIFO manner.

##### prevent_refresh (type: bool, default: true)

When set to true this option prevents duplicate entries by refresh. What it essentially does is that it does not allow an exact follow-up entry. If a refresh is detected the new entry will be discarded, thus not registering it completely.

### Driver specifics

This section holds some driver specific configurations and notes. To know what every specific driver option does please refer to the package config file ('history.php' under config folder).

#### File driver

The file driver uses a specific config key named 'file' with the following options:

```
'database' => array(
	'path' => 'path_to_store_files',
	'prefix' => 'perfix_to_use_for_files',
	'extension' => 'extension_to_use_for_files',
)

If something fails please verify the following:

* Make sure that the specified path is writeable

WARNING! (Please take a look at this or you could experience some problems):

* The File Garbage Collector will collect *ALL* files that meet the codnitions:
	- Filename starts with prefix 'prefix' and has extension 'extension'
	- File is expired using the 'threshold' value

So please make sure that you use a dedicated path for this, or the prefix is unique to the History stack to rpevent loss of other data.

#### Database driver

The table structure that the Database driver relies on is the following:

```
CREATE TABLE `history` (
  `hash` varchar(40) NOT NULL,
  `content` mediumtext,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`hash`)
) DEFAULT CHARSET=utf8
```

The database driver uses a specific config key named 'database' with the following options:

```
'database' => array(
	'table' => 'name_of_table',
	'auto_create' => boolean
)
```

#### Session driver

The Session driver uses the \Session core class to store the entries stack. This driver is intended to be used with care as it relies on the capabilities of the underlying class. This means that it dependes on the chosen driver to handle the sessions across Fuel. You should be aware of how the driver handles data and what it's limits are.

WARNING!:

* When using the 'cookie' driver for the session, the storage capacity is limited to 4kb (that means that the history stack and any other data you are storing in the session counts towards this limit). When this limit is reached you'll see this message: "The session data stored by the application in the cookie exceeds 4Kb. Select a different session storage driver". Use this with care as it may broke your app completely. You are encourage to use this combination with a small entries limit like 2 (current and previous only).

### Methods

The useful methods are listed in the following sections.

#### History class

This is the main class which handles the History stack.

##### History::push(History_Entry $entry)

Description: Pushes a History_Entry to the History stack

```
History::push($entry);
```

##### History::push_request(\Fuel\Core\Request $request)

Description: Pushes a History_Entry based on a \Fuel\Core\Request to the History stack

```
// From a Controller method
History::push_request($this->request);
```

##### History::pop()

Description: Pops the top-most (current) History_Entry from the History.
Note: this will shorten the entries by one element.

```
$entry = History::pop();
```

##### History::get_entries()

Description: Gets the entries as an array

```
$entries = History::get_entries();
foreach($entries as $entry)
{
	// do something with each entry
}
```

##### History::count()

Description: Gets the history entries count

```
$count = History::count();
if($count > 10)
{
	// do something if the entries count is greater than 10
}
```

##### History::current()

Description: Gets the current History entry

```
$entry = History::current();
echo $entry->uri;
```

##### History::previous()

Description: Gets the previous History entry

```
$entry = History::previous();
echo $entry->uri;
```

##### History::load()

Description: Loads the stored entries to the History stack using the configured driver

```
History::load();
```

##### History::save()

Description: Saves the History to a store using the configured driver

```
History::save();
```

#### History_Entry class

This class represents an entry in the History stack.

##### History_Entry::forge($data)

Description: Forges a new History_Entry object. It can take an array of data, an \Uri object or an uri string

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

The only required data option is uri. (In fact, instead of an array one could use only the uri string. All other values are automatically created) 

##### History_Entry::from_request(\Fuel\Core\Request $request)

Description: Forges a new History_Entry from a Fuel\Core\Request object

```
// From a Controller method
$entry = History_Entry::from_request($this->request);
```

##### History_Entry->serialize()

Description: Serializes the History_Entry object

```
$entry = History_Entry::forge('/');
$serialized = $entry->serialize();
```

##### History_Entry->unserialize($str)

Description: Unserializes the entries' string to the History_Entry object

```
$serialized_string = 'a:1:{i:0;C:21:"History\History_Entry":195:{a:3:{s:3:"uri";s:13:"welcome/index";s:8:"segments";a:2:{i:0;s:7:"welcome";i:1;s:5:"index";}s:8:"datetime";O:14:"Fuel\Core\Date":2:{s:12:" * timestamp";i:1317144358;s:11:" * timezone";s:3:"UTC";}}}}';
$entry = History_Entry::forge('')->unserialize($serialized_string);
```
Note: the serialized string it's just a close-to-real example

##### History_Entry::equals($compare)

Description: Compares this History_Entry instance with another and determines if they are equal.
It can be compared to a string (uri), a \Fuel\Uri object or a History_entry object.

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

### Features for 1.0 Release

The features for the next version are the ones listed here. (If you have any suggestions feel free to send them using [GitHub](https://github.com/axelitus) or send an email to dev [at] axelitus [dot] mx)

Features:

* [DONE] Configurable file prefixes for File driver (currently it takes the history_id as prefix)
* [DONE] Own method for random file name in File driver (currently tempnam() is used)
* Do some more logging when suitable for purposes of debugging
* Trigger Fuel\Core\Events when suitable
* [DONE] Garbage Collector for the File driver
* [DONE] Add the Session driver
* [DONE] Add the Database driver draft
* Create and throw History Exceptions where suitable