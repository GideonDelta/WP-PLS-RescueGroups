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
4. Use the provided widgets or shortcodes to display pets on your site. Posts can
   be flagged as **featured** or **hidden** from the post edit screen and widgets
   can optionally show featured pets first or exclusively.

## Roadmap

- Additional settings and customization options.
- Gutenberg block support.
- More extensive cleanup on uninstall.
- TODO: Sorting functionality and filters (e.g., only show featured pets).
- TODO: Optional "show random pet" feature (low priority).
