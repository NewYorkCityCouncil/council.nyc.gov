# Content Management

The NYC Council website looks one site. But it's actually a network of multiple sites, each with its own separate WordPress admin. Each of the 51 Council Districts and each Council Division (Legislation, Budget, Land Use, Press…) is its own site.

[Super Admins](https://codex.wordpress.org/Roles_and_Capabilities#Super_Admin) have access to all features on every site in the network, and access to the network administration features. Regular [Administrators](https://codex.wordpress.org/Roles_and_Capabilities#Administrator) only have access to the site(s) they've been given permission to manage.

To sign in to WordPress, simply add `/wp-admin/` to the end of site address.

When signed in, you're signed into the whole network (not a particular site) and can manage all the sites to which you've been given access. Users who have access to manage multiple sites should note that WordPress will look different (i.e. have fewer/more features available) depending on which site is being viewed. This can be particularly confusing for Super Admins. The admin bar at the top of the screen makes it easy to switch between the frontend and backend of the network. But close attention must be given to which site in the network is being viewed. If you're wondering why you can't find a feature in the WordPress admin, perhaps you're in the wrong WordPress admin.

## Admin Capabilities

Sites in the network have various features and capabilities. Here's what Administrators can do if they have access to the following sites:

### Primary Site

* Homepage (content, features, sidebar)
* Global Menu
* Widgets
* Pages
* Posts
* Committees
* Caucuses
* Initiatives
* Reports

### District

* [Districts](docs/DISTRICTS.md)

### Budget

### Legislation

### Land Use

### Press

### Jobs
