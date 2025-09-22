# Changelog

All notable changes to `php-structure-discoverer` will be documented in this file.

### 2.0.0 - 2023-08-??

- Added support for using Reflection instead of PHP token parsing to discover structures

## 2.3.2 - 2025-09-22

### What's Changed

* Fix missing single quote in file cache driver path by @SebKay in https://github.com/spatie/php-structure-discoverer/pull/29
* Update issue template by @AlexVanderbist in https://github.com/spatie/php-structure-discoverer/pull/30
* Move amphp dependencies to suggest by @riasvdv in https://github.com/spatie/php-structure-discoverer/pull/32

### New Contributors

* @SebKay made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/29
* @AlexVanderbist made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/30
* @riasvdv made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/32

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.3.1...2.3.2

## 2.3.1 - 2025-02-14

### What's Changed

* Laravel 12.x Compatibility by @laravel-shift in https://github.com/spatie/php-structure-discoverer/pull/28

### New Contributors

* @laravel-shift made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/28

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.3.0...2.3.1

## 2.3.0 - 2025-01-13

### What's Changed

* Fix parsing classes containing anonymous classes by @stevebauman in https://github.com/spatie/php-structure-discoverer/pull/27

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.2.1...2.3.0

## 2.2.1 - 2024-12-16

### What's Changed

* Fix typo in `README.md` by @PerryvanderMeer in https://github.com/spatie/php-structure-discoverer/pull/24
* Fix PHP 8.4 deprecation by @LordSimal in https://github.com/spatie/php-structure-discoverer/pull/26

### New Contributors

* @PerryvanderMeer made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/24
* @LordSimal made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/26

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.2.0...2.2.1

## 2.2.0 - 2024-08-29

- Add a new uses resolver which can be used by external packages

## 2.1.2 - 2024-08-13

- Fix issue where string or int backed enums with interfaces were not discovered
- Added extra types

## 2.1.1 - 2024-03-13

### What's Changed

* Create cache when requested (fixes #17) by @francoism90 in https://github.com/spatie/php-structure-discoverer/pull/18

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.1.0...2.1.1

## 2.1.0 - 2024-02-16

### What's Changed

* Fix doc example spacing by @stevebauman in https://github.com/spatie/php-structure-discoverer/pull/15
* Laravel 11 by @rubenvanassche in https://github.com/spatie/php-structure-discoverer/pull/16
* Dropped support for Laravel 9

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.0.1...2.1.0

## 2.0.1 - 2024-01-08

### What's Changed

* Docs - Fix config publish command by @stevebauman in https://github.com/spatie/php-structure-discoverer/pull/14
* chore: update dependencies by @jameswagoner in https://github.com/spatie/php-structure-discoverer/pull/13

### New Contributors

* @jameswagoner made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/13

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/2.0.0...2.0.1

## 2.0.0 - 2023-12-21

- Add support for discovering structures using Reflection

## 1.2.1 - 2023-08-04

- Add better support for detecting Laravel

## 1.2.0 - 2023-07-27

### What's Changed

- Add ability to sort discovered files by @stevebauman in https://github.com/spatie/php-structure-discoverer/pull/6

### New Contributors

- @stevebauman made their first contribution in https://github.com/spatie/php-structure-discoverer/pull/6

**Full Changelog**: https://github.com/spatie/php-structure-discoverer/compare/1.1.1...1.2.0

## 1.1.1 - 2023-03-24

- Add symphony finder as a requirement

## 1.1.0 - 2023-03-17

- Allow discovered structures to be built using reflection

## 1.0.1 - 2023-02-10

- Fix require with file cache driver

## 0.0.5 - 2023-02-10

Test release

## 0.0.3 - 2022-12-16

- test release

## 0.0.2 - 2022-08-18

- experimental release

## 1.0.0 - 202X-XX-XX

- initial release
