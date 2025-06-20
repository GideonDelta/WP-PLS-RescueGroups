# RescueGroups Sync

This plugin synchronizes adoptable pets from the RescueGroups.org API and registers a custom post type for displaying them.


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
3. Enter your API key and save.  
4. Choose how often to sync and/or click **Run Sync Now** for a manual sync.  
5. Set **Fetch Limit** (how many animals to pull per run; default `100`).  
6. Optionally specify **Species** and **Status** filters to limit which pets are synced.  
7. Enable **Store Raw API Data** if you want a copy of each record kept in post meta. Set the retention period in days.  
8. Use **Reset Manifest** to clear the stored ID list and re-sync everything.  
9. Customize your **Archive Slug** (default `adopt`) and **Default Query Options** (number of pets, featured-only, etc.).  
10. Use the widgets/shortcodes to display pets:  
   - Flag posts **Featured** or **Hidden** in the post editor  
   - Widgets can show featured-first or featured-only
   - The `[adoptable_pets]` shortcode supports `species`, `breed`, `orderby`, and `order` parameters

The settings page also displays the runtime and peak memory usage from the last sync to help diagnose performance issues. Any errors returned from the API will be shown next to the last sync time so you can spot connection issues.

The archive slug controls the URL of the adoptable pets archive page (default `adopt`).
Default query options set how many pets display and whether only featured pets are shown when no parameters are provided.

## Shortcode Usage

Display a list of adoptable pets anywhere on your site using the `[adoptable_pets]` shortcode:

[adoptable_pets]

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
- `rescuegroups_raw` – (optional) Trimmed API data when **Store Raw API Data** is enabled.
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

## Building the Block Script

`build/block.js` contains the JavaScript for the "Adoptable Pets" block. If you
make changes to the block source code, rebuild the script using the Node based
workflow included with this plugin:

```bash
npm install
npm run build
```

These commands run the webpack configuration provided by
`@wordpress/scripts` and output the compiled file to `build/block.js`.
Use `npm run start` while developing to automatically rebuild on changes.

## Template Customization

All HTML output now comes from PHP templates located in the `templates/` folder.
Themes can override these files by copying them to a `rescuegroups-sync` folder
inside the active theme. Components load templates using the helper
`RescueSync\Utils\Templates::render()` which accepts the template name and an
array of variables.
