# New York City Council WordPress

This repository contains the themes & plugins for [New York City Council's](http://council.nyc.gov/) WordPress multisite installation.

### Requirements

* [WordPress](https://wordpress.org/)
    * [PHP](http://php.net/)
    * [MySQL](http://mysql.com/)
* [Git](https://git-scm.com/)/[GitHub](https://github.com/)
* [HTML5](https://www.w3.org/TR/html5/)
* [Sass](http://sass-lang.com/)
* JavaScript/[jQuery](https://jquery.com/)
* [Leaflet](http://leafletjs.com/)
* [Mapbox](https://www.mapbox.com/)
* [Foundation for Sites](http://foundation.zurb.com/sites.html)
* [NPM](https://www.npmjs.com/)
* [Bower](https://bower.io/)
* [Gulp](http://gulpjs.com/)

### Local Installation

Only the themes and plugins are included in this repository. All WordPress core files are ignored. To run locally, clone this repository and manually install WordPress in the same directory, being careful not to overwrite any files.

### WordPress Multisite

This package is meant to be used with a [network](https://codex.wordpress.org/Create_A_Network) of sites. The primary site is for basic Council pages—such as the home page or about page. Each Council Districts and each Division (Legislation, Budget, Land Use, Press, etc) has its own site in the network and uses a custom child theme.

### Themes

There are several themes included in this package. The primary site uses the `wp-nycc` theme. All other themes are [child themes](https://codex.wordpress.org/Child_Themes) of the `wp-nycc` parent theme. Each District site should use the `wp-nycc-district` child theme. And each Division site should use its specific `wp-nycc-[division]` child theme.

### Foundation for Sites

The New York City Council website uses [Foundation for Sites](http://foundation.zurb.com/sites/docs/), a responsive and accessible front-end framework. The `wp-nycc` theme is based on [andycochran/wp-foundation-sites](https://github.com/andycochran/wp-foundation-sites), a bare-bones WordPress theme built with Foundation.

When upgrading to a newer version of Foundation, make sure `wp-nycc/assets/scss/_settings.scss` uses `@import '../../bower_components/foundation-sites/scss/util/util';` not `@import 'util/util';`.

### Editing Themes

* `cd wp-nycc/`
* Run `npm install` to get `node_modules`
* Run `bower install` to get `bower_components`
* Run `gulp` to compile the CSS & JavaScript
* Run `gulp watch` to watch for changes to `.sass` and `.js` files

### Styles

The styles for all themes are in the `wp-nycc` parent theme. The child themes do not include any styles.

* **Do not** edit the files in `wp-nycc/assets/css/`—they're generated by Gulp
* Add your custom styles to `wp-nycc/assets/scss/app.scss`
* Change the Foundation variables in `wp-nycc/assets/scss/_settings.scss`
* Choose which Foundation component to include in `wp-nycc/assets/scss/foundation.scss`

### JavaScript

The scripts for all themes are in the `wp-nycc` parent theme. The child themes do not include any JavaScript.

* **Do not** edit the files in `wp-nycc/assets/js/`—they're generated by Gulp
* Add/edit your custom JavaScript files in `wp-nycc/assets/js/scripts/`
* All .js files in `wp-nycc/assets/js/scripts/` will concatenate to `wp-nycc/assets/js/scripts.js` and `wp-nycc/assets/js/scripts.min.js`
* Choose which Foundation JavaScript components to include in `wp-nycc/gulpfile.js`

_Note: The `wp-nycc/map_scripts.php` and `wp-nycc-member/pb-map-scripts.php` files contain JavaScript that is dynamically produced from WordPress content and is inserted directly into the footer._

---

## Contributors

Want to add a new feature or update an existing one? The best way to contribute is to submit a pull request on GitHub. Find a bug? Please submit an issue on GitHub. You can also contact us on [Twitter](https://twitter.com/NYCCouncil) and [Facebook](https://www.facebook.com/NYCCouncil/).
