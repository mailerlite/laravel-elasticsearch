# Changelog

## 9.0.0 - 23-Feb-2022

- Support for Laravel 7, 8 and 9, and PHP 7.3–8.1 (as appropriate)


## 8.0.7 - 05-Jan-2022

- Fix `composer.json` for PHP 8 compatability (#133, thanks @adriacanal)


## 8.0.6 - 25-Oct-2021

- Add comment about `hosts.port` needing to be `null` for typical AWS connections (#126, thanks @sp-clariondoor)


## 8.0.5 - 22-Sep-2021

- Fix host assignment issue (#124, thanks @sete391)


## 8.0.4 - 28-Feb-2020

- Support for PHP 8.0 (thanks @okdewit)
  - bumps ES client version to `^7.11`


## 8.0.3 - 28-Dec-2020

- Support using a closure for AWS credentials, allowing the config to be cached (#111, thanks @stasadev)


## 8.0.2 - 02-Dec-2020

- Move automated testing from travis-ci to Github actions
- Clean up some third-party tools and badges
- Ability to create a new index with settings/mappings on `IndexCreateOrUpdateMappingCommand` (#106, thanks zeidanbm)
- Fix for loading JSON mapping files in `IndexCreateOrUpdateMappingCommand` (#104, thanks @danieljaniga)
- Set port for the request (#105, thanks @joshuaeon)
- Better error handling for request statistics (#105, thanks @joshuaeon)


## 8.0.1 - 29-Sep-2020

- Add utility console commands (huge thanks to @nsaliu)


## 8.0.0 - 10-Sep-2020

- Support for Laravel 8.0 (and drop support for earlier versions due to PHP and package conflicts) 
- Added AWS Session Token support (#87, thanks @nsaliu)


## 4.2.1 - 04-Mar-2020

- Support for custom AWS credentials (#80, thanks @giorgiomen)


## 4.2.0 - 04-Mar-2020

- Support for Laravel 7.0 (and drop support for < 6.0)


## 4.1.3 - 09-Feb-2020

- Support for AWS credentials (#76, thanks @giorgiomen)
- Speed up Travis
 

## 4.1.2 - 19-Jan-2020

- Minor fixes (add PHP 7.4 testing)


## 4.1.1 - 13-Sep-2019

- Fix for semantic versioning


## 4.1.0 - 03-Sep-2019

- Support for Laravel 6.0 (and drop support for < 5.8)


## 4.0.0 - 31-Aug-2019

- Bump ES client version to ^7.0 (#68, thanks @Lednerb)


## 3.6.0 - 26-Jun-2019

- Add support for tracers (#65, thanks @luoxiaojun1992)
- Fix typo in README.md (#63, thanks @Harrisonbro)


## 3.5.1 - 29-Apr-2019

- Fix for ElasticSearch 6.5 (#59, thanks @SirNarsh)


## 3.5.0 - 04-Mar-2019

- Support Laravel/Lumen 5.8


## 3.4.0 - 17-Dec-2018

- Re-add Lumen support (thanks @ristedavcevski)
- Fix to AWS host logic (@thanks @tufanbarisyildirim)


## 3.3.0 - 12-Dec-2018

- Support AWS hosts (thanks @Matrix86)
- Add ability to register additional namespaces (thanks @ShaneXie)


## 3.2.0 - 04-Sep-2018

- Support Laravel/Lumen 5.7


## 3.1.1 - 28-Feb-2018

- Support Laravel/Lumen 5.6 (thanks @akira28)


## 3.1.0 - 14-Dec-2017 

- Real fix for Lumen users (thanks @petercoles and @aripekkako)


## 3.0.0 - 29-Nov-2017

- Bump ES client version to ^6.0


## 2.1.0 - 31-Aug-2017

- Laravel 5.5 support, including auto-registration
- Bump ES client version to ^5.3


## 2.0.1 - 14-Jul-2017

* Add alias for Lumen (thanks @matejvelikonja)


## 2.0.0 - 04-May-2017

* Bump ES client version to 5.x
* Allow configuration via `.env` file (thanks @expertcoder)


## 1.3.0 - 17-Feb-2017

* Further fixes for Lumen (thanks @pafelin).


## 1.2.0 - 20-Jun-2016

* Fixed support for Lumen (thanks @JamesB797).
* Fix for omitting connection configuration (thanks @xian13).
* PSR-2 code style changes.
* Cleaned up Lumen service provider, and refactored configuration setting in Factory.


## 1.1.0 - 11-Apr-2016

* Added support for Lumen (thanks @khoatran).


## 1.0.0 - 06-Jan-2016

* First "stable" release.
* Replace `bindShared` with `singleton` in the service provider to make it Laravel 5.2 compatible (#5 thanks @goldlife)


## 0.9.1 - 04-Jun-2015

* Remove index mangling from this package (that functionality belongs elsewhere).


## 0.9.0 - 27-May-2015

* Initial tagged release.
