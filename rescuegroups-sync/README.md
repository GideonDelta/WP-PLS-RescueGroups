# RescueGroups Sync

This plugin synchronizes adoptable pets from the RescueGroups.org API and registers a custom post type for displaying them.

**Work in progress.**

## Installation

1. Upload the `rescuegroups-sync` directory to your `/wp-content/plugins/` folder.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Ensure your server is running PHP 7.2 or greater.

## Configuration

1. Obtain an API key from [RescueGroups.org](https://rescuegroups.org/).
2. In the WordPress admin, go to **Rescue Sync** under **Settings**.
3. Enter your API key and save the settings.
4. Use the provided widgets or shortcodes to display pets on your site.

## Available Fields

Each synced pet stores the following meta fields:

- `rescuegroups_id` – RescueGroups.org identifier.
- `rescuegroups_raw` – Raw API response for the record.
- `_rescue_sync_species` – Species name.
- `_rescue_sync_breed` – Primary breed.
- `_rescue_sync_age` – Age group or string.
- `_rescue_sync_gender` – Gender/sex value.
- `_rescue_sync_photos` – JSON encoded array of photo URLs.

The plugin also registers `pet_species` and `pet_breed` taxonomies for better filtering.

## Roadmap

- Additional settings and customization options.
- Gutenberg block support.
- More extensive cleanup on uninstall.
