# RescueGroups Sync

This plugin synchronizes adoptable pets from the RescueGroups.org API and registers a custom post type for displaying them.

**Work in progress.**

## Requirements

- WordPress 5.6 or newer
- PHP 7.2 or newer
- A RescueGroups.org API key
- WP‑Cron enabled so the hourly sync task can run

## Installation

1. Download or clone this repository.
2. Upload the `rescuegroups-sync` directory to your `/wp-content/plugins/` folder (or upload the ZIP via **Plugins > Add New**).
3. In the WordPress admin, go to **Plugins** and click **Activate** under **RescueGroups Sync**.
4. On activation the plugin registers the `adoptable_pet` post type and schedules an hourly sync event.

## Configuration

1. Obtain an API key from [RescueGroups.org](https://rescuegroups.org/).
2. In the WordPress admin, go to **Rescue Sync** under **Settings**.
3. Enter your API key and save the settings.
4. Use the provided widgets or shortcodes to display pets on your site.

## Roadmap

- Additional settings and customization options.
- Gutenberg block support.
- More extensive cleanup on uninstall.

## Uninstall

Deleting the plugin from the **Plugins** screen triggers the `uninstall.php` script.
Currently this removes the stored API key option. Future updates will also remove
custom posts and metadata created by the plugin so that your database is left clean.
