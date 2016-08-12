# Districts

Each Council District has its own site in the WordPress multisite network. This is so that editors of District content do not have access to the content of other Districts or Divisions. This also simplifies the admin so that editors can more easily perform their tasks. _Note: Super Admins have access to all network sites._

When setting up the WordPress network, a site should be created for each Council District using the "NYCC District" theme (`wp-nycc-district`), which is a child theme of the "New York City Council" theme (`wp-nycc`).

## District Options

Each District site's admin requires some configuration in the District's Admin > Settings > District Options. There you will define the following options:

* District Number - the **unique** District number
* Council Member
    * Full Name - first name, last name, and optional middle initial and/or suffix—e.g. "Bill S. Preston Esq." (**do not include titles**)
    * Short Name - typically last name only, appears in strings such as "Council Member [last name]"
* Short Bio - the text that appears at the top of the District front page
* Thumbnail - the absolute URL to a the Council Member's headshot (preferably uploaded via the District's Admin > Media)
* Party - enter Republican, Democrat, etc.
* Borough - e.g. "Queens" or "Manhattan, Bronx"
* Neighborhoods - a list of neighborhoods that fall into the District, used by District list search
* Contact Info - address, phone, fax, etc. for both the District Office and Legislative Office, appears in the District sidebar
* Email Address *(optional)* - if set, an email link button appears under the contact info in the District sidebar

## District Front Page

The District Front Page displays the short bio (from District Options), followed by lists of the Committees and Caucuses on which the District's Member sits, followed by the page content.

When setting up the District site, a page titled "District [number]" must be created for the front page. Then, in the District's Admin > Settings > Reading, the "Front page displays" settings should set this page as the "Front page." Optionally, another page ("News") can be created to be set as the "Posts page."

The front page must include a Featured Image, which will display next to the map in the District Header.

## District Sidebar
The District's sidebar is present on all of the District's pages and posts. The sidebar is composed of a menu area followed by a widget area.

### Menu

The sidebar menu is edited in the District's Admin > Appearance > Menus. A menu set to the "Primary Menu" location will appear at the top of the sidebar. If the District is taking part in Participatory Budgeting, add a custom link with the URL set to `/district-[number]/pb/` (the ballot item archive permalink).

### Widgets

Sidebar widgets are edited in the District's Admin > Appearance > Widgets. The "Contact Information" widget gets its info from the District Options.
