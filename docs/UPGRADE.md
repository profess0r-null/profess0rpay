# Upgrade Guide

## Upgrading to v1.0.0

This is the initial open-source release of Profess0rPay.

For future versions, this file will contain detailed instructions on:
- How to back up your database.
- Which files to overwrite during an update.
- How to run database migrations.

### General Upgrade Process

**Option 1: Over-The-Air (OTA) Updates (Recommended)**
Starting from v1.3.0, you can update Profess0rPay directly from the Admin Dashboard!
1. Navigate to **System Settings > Update**.
2. Click the update button to automatically fetch and install the latest stable release.

**Option 2: Manual Upgrade**
1. Backup your existing `Profess0rPay` directory and Database.
2. Download the latest release from the official repository.
3. Replace the old files with the new files (excluding your `assets/` and custom config files if applicable).
4. Run the installer/updater script to migrate the database schemas.
