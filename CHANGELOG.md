# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - Unreleased

### Added
- Laravel 12 support
- `morphedByMany` override for inverse polymorphic many-to-many relationships
- `initializeHasCustomMorphMap()` method following Laravel's trait initialization convention
- PHPStan static analysis at max level
- Laravel Pint code style enforcement
- Comprehensive test suite with database integration tests
- CI matrix testing across PHP 8.1-8.4 and Laravel 10-12
- `.editorconfig` for consistent formatting
- `CONTRIBUTING.md` with contribution guidelines

### Changed
- **Breaking:** Minimum PHP version is now 8.1 (was 8.0)
- **Breaking:** Minimum Laravel version is now 10.0 (was 8.12)
- Renamed internal `customMorphMap()` method to `resolveCustomMorphType()` to avoid name collision with property
- Default morph type now uses `static::class` instead of `__CLASS__` for better inheritance support
- Replaced `func_get_args()` usage with explicit parameter forwarding
- Updated `phpunit.xml` to `phpunit.xml.dist` with modern PHPUnit 10/11 configuration

### Fixed
- Test models had incorrect `customMorphMap` key format (keys should be class FQCNs, values should be aliases)

### Removed
- Dropped support for PHP 8.0
- Dropped support for Laravel 8.x and 9.x
- Removed hardcoded `version` field from `composer.json`

## [1.0.4]

- Fix README after ownership change.

## [1.0.3]

- Change ownership from **mcucen** to **moneo**.
