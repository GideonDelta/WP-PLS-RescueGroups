# RescueGroups Sync

This plugin synchronizes adoptable pets from the RescueGroups.org API and registers a custom post type for displaying them.

**Work in progress.**

## Requirements

- WordPress 5.6 or newer
- PHP 7.2 or newer
- A RescueGroups.org API key
- WP-Cron enabled so the hourly sync task can run

## Installation

1. Download or clone this repository.  
2. Upload the `rescuegroups-sync` directory to your `/wp-content/plugins/` folder (or upload the ZIP via **Plugins > Add New**).  
3. In the WordPress admin, go to **Plugins** and click **Activate** under **RescueGroups Sync**.  
4. On activation the plugin registers the `adoptable_pet` post type and schedules an hourly sync event.

## Configuration

1. Obtain an API key from [RescueGroups.org](https://rescuegroups.org/).  
2. In the WordPress admin, go to **Rescue Sync** under **Settings**.  
3. Enter your API key and save the settings.
4. Choose how often the sync should run and optionally trigger a manual sync.
5. Set how many animals are fetched during each sync (default `100`).
6. Use the **Reset Manifest** button to clear the stored ID list if needed.
7. Customize the adoptable pets archive slug and default query options.
8. Use the provided widgets or shortcodes to display pets on your site.
   - Posts can be flagged as **featured** or **hidden** from the post edit screen.
   - Widgets can optionally show featured pets first or exclusively.
   - Species, breed and ordering options mirror the `[adoptable_pets]` shortcode.

The archive slug controls the URL of the adoptable pets archive page (default `adopt`).
Default query options set how many pets display and whether only featured pets are shown when no parameters are provided.

## Shortcode Usage

Display a list of adoptable pets anywhere on your site using the `[adoptable_pets]` shortcode:

[adoptable_pets]

pgsql
Copy
Edit

### Parameters

- `number` – Number of pets to show. Default is `5`.
- `featured_only` – Set to `1` to show only pets marked as featured.
- `random` – Set to `1` to show pets in random order.
- `species` – Comma separated list of species slugs to include.
- `breed` – Comma separated list of breed slugs to include.
- `orderby` – Field to sort by (`date`, `title`, or `rand`).
- `order` – Sort direction, either `ASC` or `DESC`.

Example (eight featured pets):

[adoptable_pets number="8" featured_only="1"]

To display a single random pet you can use the `[random_pet]` shortcode which internally calls `[adoptable_pets random="1" number="1"]`.

markdown
Copy
Edit


### Counting Pets

Use `[count_pets]` to display how many pets match a species and status.
`type` accepts a species slug and `status` defaults to `adoptable`.

Example:

```
[count_pets type="dog" status="adoptable"]
```

## Block Usage

An "Adoptable Pets" block is available in the Block Editor under the Widgets category.
Add the block to any post or page and choose how many pets to display. Enable the
"Only show featured" option to limit the list to featured animals.


## Available Fields

Each synced pet stores the following meta fields:

- `rescuegroups_id` – RescueGroups.org identifier.  
- `rescuegroups_raw` – Raw API response for the record.  
- `_rescue_sync_species` – Species name.  
- `_rescue_sync_breed` – Primary breed.  
- `_rescue_sync_age` – Age group or string.  
- `_rescue_sync_gender` – Gender/sex value.  
- `_rescue_sync_photos` – JSON-encoded array of photo URLs.

The plugin also registers `pet_species` and `pet_breed` taxonomies for better filtering.

## Sync Manifest

Each sync stores a manifest of imported animals in the `rescue_sync_manifest` option. The plugin compares incoming data against this manifest and skips updating posts when nothing has changed.

## Roadmap

- Additional settings and customization options.  
- Gutenberg block support.  
- More extensive cleanup on uninstall.
- Sorting functionality and filters (e.g., only show featured pets).

## Uninstall

Deleting the plugin from the **Plugins** screen triggers the `uninstall.php` script.
The uninstall routine now removes all `adoptable_pet` posts, deletes terms from
the `pet_species` and `pet_breed` taxonomies, erases any metadata beginning with
`_rescue_sync_`, clears scheduled sync events and deletes all plugin options.
This leaves no orphaned data behind after removing the plugin.
