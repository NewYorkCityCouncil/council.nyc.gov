# The Map

Do you know which Council Member/District is yours? Many of our neighbors don't. To address this problem, an interactive map is available on every page/post. This makes it convenient and easy for users to discover their Member/District, no matter which part of the site they are viewing. When a user clicks on the map, a popup gives them information about that District, which is defined in the [District Options](DISTRICTS.md#district-options).

## Map Scripts

The `wp-nycc` theme concatenates all of its JavaScript into `scripts.js` and `scripts.min.js` via Gulp. However, the map scripts are not included in this concatenation. Instead, the following PHP files add inline JavaScript in the footer:

* `wp-nycc/map_scripts.php` - loaded on every page of the site via `footer.php`
* `wp-nycc/assets/js/nyccouncil-districts.js` - GeoJSON loaded in `map_scripts.php`
* `wp-nycc-district/pb-map-scripts.php` - loaded when viewing a District's Participatory Budgeting page

These files contain JavaScript that's generated dynamic via PHP. This is so that the JavaScript can loop through WordPress data to get popup info, PB info, etc.

_Note: The "Map Toggler" function, which expands the height of the slim map on non-District pages, is located in `wp-nycc/app.js`._
