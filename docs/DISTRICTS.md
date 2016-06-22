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

## Participatory Budgeting

If a District is taking part in Participatory Budgeting, you can add ballot items in the District's Admin > PB Ballot.

### Ballot Items

Ballot items are a custom post type. They're similar to posts, except there is no single ballot item view. Instead, ballot items appear in the ballot item archive at `/district-[number]/pb/`. If you try to go to the permalink of a ballot item, you'll be redirected to the archive.

Edit the content as you would a regular post. The following settings will determine how the ballot item displays:

* **PB Categories** - appears as a label in the top right corner of the ballot item (Note: Categories are a comma-separated list. So if you try to add a category that contains a commas—e.g. "Arts, Culture, & Community"—it will add multiple categories. You have to add the category without the commas, then edit the category in the District's Admin > PB Ballot > Categories. Or avoid using commas—e.g. "Arts/Culture/Communty.")
* **Order** - order by which the ballot item appears in lists (falls back to reverse chronological published date)
* **Winning Project** - before voting, leave all items set to "No"; once voting has ended, set the winners to "Yes"

### Vote Sites

Vote sites are a custom post type. They're similar to posts, except there is no single vote site view. Vote sites appear as interactive markers on the District map and—if there are no winning projects—in a list alongside the ballot items.

Edit the content as you would a regular post. The following settings will determine how the vote site displays:

* **Order** - order by which the vote site appears in lists (falls back to reverse chronological published date)
* **Latitude & Longitude** - position of the map marker (PROTIP: Get the Lat/Lon by searching a place in Google Maps and copying the first two numbers after the `@` in the URL. Don't drag the map or these numbers will change.)

### Ballot Item Archive

The layout of the ballot item archive depends on content:

* **Before Winners Are Announced:** If no winning projects exist (every ballot item's "Winning Project" value is set to "No"), the archive has a two-collumn layout listing the ballot items alongside the vote sites.
* **After Winners Are Announced:** If one or more winning project exists (a ballot item "Winning Project" value is set to "Yes"), the archive lists the "Winning Projects" followed by a list of projects that were not funded. Vote sites are no longer listed on the page (although they will appear on the map until they're deleted).

### PB Page Templates

On the primary site, the following page templates are available for displaying PB info aggregated from all Districts:

* PB Districts List - _displays alongside the page content a District lookup widget and a list of Districts that have PB projects_
* PB Results - _displays page content followed by a list of winning PB projects ordered by District_
* PB Sidebar - _displays the PB Sidebar widgets alongside the page content_
