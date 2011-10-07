# Fuel History package changelog

Latest version: 1.0.2

## v1.0.2

Description: This is a maintenance/feature patch that fixes some issues and introduces small features.

Released: 07/10/2011

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

Description: This is a maintenance patch that removes some features that just overcomplicated things. [Thanks to [FrenkyNet](https://github.com/FrenkyNet) for he's input in this matter].

Released: 05/10/2011

### Fixes

* Eliminated all the events as they didn't add value to the History package worflow and just complicated things.

### Features

* Added a VERSION constant in the History class.