CHANGELOG
=========

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), and this project adheres
to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

2.0.0 - July 2021
-----------------

* Deprecated `SwivelComponent::initSwivelTable` method and associated `SwivelComponent::$autoCreateSwivelTable`
  property. This functionality is used by the legacy method for creating the swivel table and should no longer be used.
* Added test suite to the repo, with supporting docker stack for testing.
* Added this Changelog
* Added SwivelDataSource interface for compatibility with custom data source classes
* Added sanity checking with defaults for the config options in the SwivelLoader
* Adopted true SemVer for future changes
* Changed to implement strict typing for the package, with an eye towards php8 compatibility.
* Changed the underlying swivel library package dependency (bumped from 2.x to 4.x)
* Changed the underlying Yii Framework version dependency (Added the 3.x option)
* Changed minimum PHP version to 7.4
* Changed directory structure to be compatible with the Yii 3 method of separating namespaced content from resources.
* Changed component references to leverage Yii's DI container

1.3.0 - December 2019
---------------------
* Changed the underlying Yii Framework version dependency (moved from * to 2.0.13 constraint)
* Changed to add support for PHP 7.2
* Changed parent class for the SwivelComponent to match the Yii Framework changes

1.2.0 - August 2019
------------------
* Changed table creation to migration - matching Yii 2 practice of migration vs interrogation

1.1.0 - January 2019
--------------------
* Added support for alternate component names
* Changed to match Yii2 coding standards
* Changed log level to int

1.0.1 - October 2016
------------------
* Added sanity check for null buckets

1.0.0 - March 2016
----------------
* Branched to support Yii 2