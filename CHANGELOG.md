# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.2] - 2026-07-13
### Added
- Integrated PHPMailer to bypass native `mail()` restrictions on shared hosting environments (cPanel/LiteSpeed).
- Added a fully functional graphical SMTP Settings panel in the Admin Dashboard (Brand Settings -> SMTP Settings).
- Added an embedded "SMTP Setup Guide" with bilingual instructions (Bengali/English) to assist users with Gmail and cPanel SMTP configuration.
- Added comprehensive Email & SMTP Documentation to the official `README.md`.

### Fixed
- Fixed critical email delivery bugs causing silent failures when `mail()` was restricted by hosting providers.
- Refactored MFS gateway checkout instructions to dynamically output correct actions (Send Money for Personal, Payment for Merchant, Cash Out for Agent, and Fund Transfer for Cellfin).
- Removed redundant debugging files and optimized the release packaging script.

## [1.3.0] - 2026-07-10
### Added
- Added visual red dot badge for unread notifications in the admin header.
- Fully rebranded all 6 third-party integration plugins (WordPress, WHMCS, SMM) to Profess0rPay.
- Implemented a new Gateway API URL field in all plugins to allow dynamic configuration for self-hosted instances.

### Fixed
- Fixed admin notification JS routing (switched to GET for compatibility).
- Fixed checkout status polling to automatically update the UI every 3 seconds without a full page refresh.
- Fixed the "Make Another Payment" button styling which was overwritten during AJAX status updates.
- Fixed missing favicon in checkout status page to dynamically load the brand's favicon.
- Fixed notification bell visibility issue on mobile devices.
- Fixed "Revenue Overview" graph rendering issue on the dashboard during AJAX navigation.
- Fixed Javascript errors blocking smart redirects after creating or editing a gateway.
- Fixed fresh installation database version mismatch (defaulting to 1.3.0 instead of 1.2.2).
- Fixed checkout auto-verification polling bug where TrxID mode and SMS validity checks were completely ignored by the background AJAX request.
- Fixed SMS regex parsing rules by adding robust fallbacks for bKash, Upay, Tap, Cellfin, OKWallet preventing missing SMS logs.
- Fixed cancel redirect page UI cross icon thickness.
- Updated WooCommerce plugin to bundle the favicon locally.
- Added plugins/ directory for pre-packaged integrations.
## [1.2.3] - 2026-07-09
### Added
- Added domain whitelist security validation for popup checkouts to prevent unauthorized cross-origin payment requests.
- Added dynamic 'Copy to Clipboard' buttons with toast notifications for Transaction ID and Payment Link fields in the Admin Transaction Details modal.
- Added 'Force Re-install' capability to the OTA System Updater, allowing admins to re-download and re-install the current version without needing a version bump.
- Added real-time dynamic AJAX Notification System to the admin dashboard.
- Added new notification triggers: Payment Success, Admin Security Alert, Device Offline Alert, and System Update Available Alert.

### Fixed
- Fixed an API payload bug where popup checkouts failed to capture and save `return_url` in the database.
- Added backward compatibility in the API payload to accept `redirect_url` and `cancel_url` as fallbacks for `return_url`.
- Redesigned Admin Transaction Details modal for a cleaner, compact view by removing unnecessary Bootstrap card headers and box shadows.
- Rearranged transaction data blocks to categorize Financial Data, System Data, and Customer Details logically.

## [1.2.2] - 2026-07-08
### Added
- Added Dynamic Numeric Routing: Visiting purely numeric routes (e.g. `/30`) redirects to the default payment link with a fixed amount. Can be toggled from Admin General Settings.
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
