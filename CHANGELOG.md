# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.2] - 2026-07-08
### Fixed
- Fixed auto-updater bug in `process.php` causing incorrect version checks from the settings table instead of the env table.
- Fixed broken gateway logo previews in the admin panel for locally hosted images.
- Increased font size and adjusted spacing for dashboard action buttons to improve readability on mobile devices.
- Removed the 0.1-second flashing auto-redirect on canceled or rejected payment screens to allow users to review the transaction status or initiate a new payment manually.

## [1.2.1] - 2026-07-08
### Added
- Added custom toast notifications for copying items.
- Added download receipt functionality on the checkout success page.
### Fixed
- Fixed HTML syntax errors in brand settings.
- Restructured `pp_version` from the settings table to the new `env` table.

## [1.0.0] - 2026-07-01
### Added
- Initial Open-Source Release (Profess0rPay).
- 2-Minute Installer (`pp-install`).
- White-label branding capabilities from the Admin panel.
- Built-in payment gateway management (MFS and Manual).
- REST API and Payment Link generation.
- Dynamic check-out page UI based on brand colors.
