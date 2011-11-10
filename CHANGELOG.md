# Fuel History package changelog

**Latest version:** 1.1

## v1.1

**Description:** This release introduces some major changes, one of them about support of FuelPHP version.

**Released:** 10/11/2011

### General changes

* Updated code to FuelPHP v1.1. Support for FuelPHP prior to v1.1 is dropped. Please update your FuelPHP version.

### Fixes

* History\_entry::get\_segment() now works like Fuel\Uri::get_segment() [one-based segment's array indexes]
* Removed unnecessary code

### Features

* New exclude array to exclude certain Request Uris.

## v1.0.3

**Description:** This is a maintenance/feature patch that centralizes some repeated code execution and introduces compression feature.

**Released:** 07/10/2011

### Fixes

* Centralized some repeated code execution.

### Features

* Added compression feature.

## v1.0.2

**Description:** This is a maintenance/feature patch that fixes some issues and introduces small features.

**Released:** 07/10/2011

### Fixes

* Fixed a bug where previous() returned the same as current().
* Got rid of some unnecesarry code.
* Changed Exceptions that are thrown by classes.
* Added the missing classes (History Exceptions) to the bootstrap file.

### Features

* Added the get_segment() method.
* Added referrer and post (hash and data) to the History_Entry class.
* Added the get_Entry() method.

## v1.0.1

**Description:** This is a maintenance patch that removes some features that just overcomplicated things. [Thanks to [FrenkyNet](https://github.com/FrenkyNet) for he's input on this matter].

**Released:** 05/10/2011

### Fixes

* Eliminated all the events as they didn't add value to the History package worflow and just complicated things.

### Features

* Added a VERSION constant in the History class.

## v1.0

**Description:** First release.

**Released:** 05/10/2011