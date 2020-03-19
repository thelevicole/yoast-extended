# Yoast SEO Extended

> ⚠️ In progress - not for use!

This idea is that this plugin will add all the features the core [Yoast SEO plugin](https://yoast.com/wordpress/plugins/seo/) is missing.

## Current features
### 1. A better bulk editing tool.
Yoast SEO includes a bulk editor in the core found under `SEO > Tools > Bulk editor` but I have found this clunky to use and more importantly only supports editing for post type values **not taxonomies**!

This plugin includes support for editing terms in addition to posts.

### 2. Unique term values for post types
Imagine you have a taxonomy shared across two post types, for example:

- Taxonomy: `Locations`
- Post types: `Venues`, `Suppliers`

Now let's say you allow users to access the location taxonomy archive for both post types, e.g.:

- `http://example.com/{post_type}/{location_term}/`
- - `http://example.com/venues/london/`
- - `http://example.com/suppliers/london/`

The issue with the core Yoast SEO functionality is that both of these URLS share the same meta data. With Yoast Extended you can specify meta titles and descriptions per post type/taxonomy pairings.

## Future features
❓❓ Open to suggestions.
