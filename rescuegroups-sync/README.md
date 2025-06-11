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
4. Use the provided widgets or shortcode to display pets on your site.

## Shortcode Usage

Display a list of adoptable pets anywhere on your site using the `[adoptable_pets]` shortcode.

```
[adoptable_pets]
```

### Parameters

- `number` - Number of pets to show. Default is `5`.
- `featured_only` - Set to `1` to show only pets marked as featured.

Example showing eight featured pets:

```
[adoptable_pets number="8" featured_only="1"]
```

## Roadmap

- Additional settings and customization options.
- Gutenberg block support.
- More extensive cleanup on uninstall.
