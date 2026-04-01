# Changelog

All notable changes to `filament-kurdish-calendar` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- CI (Pint + Pest + PHPStan).
- Pest/Testbench coverage for the converter.

### Changed
- Nawroz anchor (March 21) and base month lengths are enforced in code (not configurable).
- Package config defaults are static (no env usage).

### Breaking
- PHP namespace is now `Rawand201\FilamentKurdishCalendar\...` (was `Rawand\FilamentKurdishCalendar\...`). Update all `use` statements and class references.

