# Participatory Budgeting

PB content is managed on both the main site (with pages using specific templates) and participating Districts sites (through ballot items, vote sites, and taxonomies).

Content management follows the general PB process:

* **Cycle Kickoff**
  * When the Districts taking part are announced, a matching PB Cycle taxonomy term (e.g. "6" representing Cycle 6) is created for each of those Districts
  * On the main site, a page with a specific template displays the Districts that are taking part (those which use the matching term associated with the cycle)
* **Potential Projects Announced**
  * Ballot items are added on each participating District's site
* **Voting**
  * Vote sites are added on each participating District's site
* **Winners Announced**
  * Specific ballot items are marked as winning on each participating District's site
  * On the main site, a page with a specific template displays the winning projects from each District

## District Ballot Items

Add ballot items in the District's Admin > PB Ballot. Ballot items are a custom post type similar to posts, except there is no single ballot item view. Instead, they appear in an archive view at `/district-[number]/pb/[cycle]/`.

Edit the content as you would a regular post. The following settings will determine how the ballot item displays:

* **PB Categories** - appears as a label in the top right corner of the ballot item. _Note: Categories are a comma-separated list. So if you try to add a category that contains commas (e.g. "Arts, Culture, & Community") it will add multiple categories. You have to add the category without the commas, then edit the category in the District's Admin > PB Ballot > Categories. Or avoid using commas—e.g. "Arts/Culture/Communty."_
* **PB Cycle** - a custom taxonomy like tags; determines which PB cycle the ballot item is associated with; must be a number (e.g. "6")
* **Order** - order by which the ballot item appears in lists (falls back to reverse chronological published date)
* **Winning Project** - before voting, leave all items set to "No"; once voting has ended, set the winners to "Yes"

## District Vote Sites

Add vote sites in the District's Admin > PB Vote Sites. Vote sites are a custom post type similar to posts, except there is no single Vote Sites view. Vote sites appear as interactive markers on the District map and—if there are no winning projects—in a list alongside the ballot items. Vote sites are not associated with a specific PB cycle, so they can be reused in future cycles if they remain applicable (or deleted if they're no longer applicable).

Edit the content as you would a regular post. The title of the the vote site should be a location name (e.g. "District Office" or "Carl Schurz Park"). The content should list the vote site's time(s), date(s), and street address (if applicable). The following settings will determine how the vote site displays:

* **Order** - order by which the vote site appears in lists (falls back to reverse chronological published date)
* **Latitude & Longitude** - position of the map marker (PROTIP: Get the Lat/Lon by searching a place in Google Maps and copying the first two numbers after the `@` in the URL. Don't drag the map or these numbers will change.)

## District PB Cycles

Ballot items and vote sites for a particular cycle are viewed at `/district-[number]/pb/[cycle]/`. The layout of this page depends on the following conditions:

* **No Ballot Items Exist:** If a PB Cycle taxonomy term exists but there are no ballot items, the page displays generic content set in the main site's Admin > Settings > Site Options > PB Placeholder Markup.
* **Before Winners Are Announced:** If no winning projects exist (every ballot item's "Winning Project" value is set to "No"), the archive has a two-collumn layout listing the ballot items alongside the vote sites.
* **After Winners Are Announced:** If one or more winning project exists (a ballot item "Winning Project" value is set to "Yes"), the archive lists the "Winning Projects" followed by a list of projects that were not funded. Vote sites are no longer listed on the page (although they will appear on the map until they're deleted).

## Main Site Page Templates

Regular pages on the main site can use PB page templates and a custom field called `current_pb_cycle` to pull in content from District sites in which a matching PB Cycle taxonomy term exists (in each District's Admin > PB Ballot > PB Cycle).

* Create a page on the main site
* Select a PB template in Page Attributes > Template
* Add a custom field with the name `current_pb_cycle` and a value that matches the "PB Cycle" taxonomy term on the participating Districts

_Note: if you can't see the Custom Field panel, make sure it's turned on in the Screen Options menu (top right of the Admin)._

### Template: PB Districts List

A page on the main site using the `page-pbdistricts.php` template will display alongside its content a District lookup widget and a list of Districts that are taking part in a particular PB cycle.

### Template: PB Results

A page on the main site using the `page-pbresults.php` template will display below its content a list of winning projects from a particular PB cycle.

### Template: PB Sidebar

A page on the main site using the `page-pbsidebar.php` template will display the PB Sidebar widgets alongside the page content

### PB Page Menus

All PB page templates include a menu for navigating between pages which have identical values for the `pb_cycle_menu` custom field. The order of menu items is set via Page Attributes > Order.
