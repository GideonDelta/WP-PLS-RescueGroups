# Development TODO

The following tasks come from the plugin README:

- Additional settings and customization options.
- Gutenberg block support.
- More extensive cleanup on uninstall.
- Sorting functionality and filters (e.g., only show featured pets).
- Optional "show random pet" feature (low priority).
- Future updates will also remove custom posts and metadata created by the plugin so that your database is left clean.

## Shortcode Idea

Add a shortcode that counts pets by type and status:

```
[pet_count type="dog" status="available"]
```

The shortcode should accept `type` and `status` attributes to filter the count returned.
