# Testing Checklist

Use this checklist when validating the RescueGroups Sync plugin.

- Verify that the test environment is running a supported version of WordPress and PHP.
- Obtain a RescueGroups.org API key for connecting to the service.
- Build the block script using `npm install` and `npm run build` if the source was modified.
- Install and activate the plugin on the WordPress site.
- Configure the plugin settings including the API key and desired sync options.
- Note any template overrides made in the active theme.
- Perform an initial sync and test the shortcode and block output.
