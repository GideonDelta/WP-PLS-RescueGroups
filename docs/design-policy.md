# Plugin Design Policy

This document summarizes guidelines for structuring code in the RescueGroups Sync plugin.

## Separation of Logic and Markup

Classes should not combine business logic with HTML output. When a component needs to display markup, it should delegate rendering to a template file. This keeps classes focused on data preparation and simplifies testing.

### Templates Directory

All markup lives in the `/templates` directory at the plugin root. Templates are plain PHP files that receive data and output HTML. Use the `Templates::render()` helper to load a template and pass variables:

```php
Templates::render( 'admin/settings-page', [ 'pets' => $pets ] );
```

### Required Usage

Admin pages, widgets, blocks, shortcodes, and metaboxes must output HTML exclusively through templates. Their classes should gather data and then call `Templates::render()` to display the markup.
