# Districts

Each Council District has its own site in the WordPress multisite network. This is so that editors of District content do not have access to the content of other Districts or Divisions. This also simplifies the admin so that editors can more easily perform their tasks. District sites use the "NYCC District" theme (`wp-nycc-district`), a child theme of the "New York City Council" theme (`wp-nycc`).

_Note: Super Admins have access to all network sites_

## District Options

When setting up the WordPress network, a site should be created for each Council District using the `wp-nycc-district` theme. Each District site's admin requires some configuration in Settings > District Options. There you will define the following options:

* District Number - the **unique** District number
* Council Member
    * Full Name - first name, last name, and optional middle initial and/or suffix, **do not include titles** (e.g. "Bill S. Preston Esq.")
    * Short Name - typically last name only, appears in strings such as "Council Member [last name]" (e.g. "Preston")
* Short Bio - the text that appears at the top of the District front page
* Thumbnail - the absolute URL to a the Council Member's headshot (preferably uploaded via WordPress Admin > Media )
* Party - enter Republican, Democrat, etc.
* Borough - e.g. "Queens" or "Manhattan, Bronx"
* Neighborhoods - a list of neighborhoods that fall into the District, used by District list search
* Contact Info - address, phone, fax, etc. for both the District Office and Legislative Office, appears in the District sidebar
* Email Address *(optional)* - if set, an email link button appears under the contact info in the District sidebar

## District Front Page

When setting up the District site, a page titled "District [#]" should be created for the front page. Its Featured Image will display next to the map in the District Header. Under Settings > Reading, the front page should be set to display that page. Optionally, another page ("News") can be created to be set as the "Posts page."

The District Front Page template displays the short bio (from District Options), followed by lists of the Committees and Caucuses on which the District's Member sits, then the page content.

## District Sidebar
The District's sidebar is present on all of the District's pages and posts. The sidebar is composed of a menu area followed by a widget area.

### Menu

The sidebar menu is edited in the District's admin under Appearance > Menus. A menu set to the "Primary Menu" location will appear at the top of the sidebar.

### Widgets

Sidebar widgets are edited in the District's admin under Appearance > Widgets. The "Contact Information" widget gets its info from the District Options.

## Participatory Budgeting

adding ballot items and categories
adding vote sites
adding a district sidebar menu item
winning projects results
main site's PB templates (districts, results, sidebar)
