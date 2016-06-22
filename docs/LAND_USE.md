# Land Use

The Land Use Division has its own site in the WordPress multisite network. This is so that editors of Land Use content do not have access to the content of other Divisions. This also simplifies the admin so that editors can more easily perform their tasks. The Land Use site uses the "NYCC Land Use" theme (`wp-nycc-land-use`), a child theme of the "New York City Council" theme (`wp-nycc`). _Note: Super Admins have access to all network sites._

## Plans

Plans are a custom post type, similar to pages. To add/edit a Plan, go to the Land Use site's Admin > Plans. Edit the content as you would a regular page. The following settings will determine how the plan displays:

* **Excerpt** *(optional)* - appears as a subheading under the title (excerpts also appear in search results, which default to the first ~55 words of the content if the excerpt is empty)
* **Attributes**
    * **Parent** *(optional)* - plans can be hierarchical; if a parent is set, a link to the plan and its excerpt will display alongside the parent plan's content
    * **Order** - order by which child plans appear in their parent plan
* **Event Meta** *(optional)* - if there is an event (such as a public hearing) associated with the plan  
    * **Event Location** - where the event is located
    * **Event Map Link** - an external link to directions (e.g. City Hall on Google Maps is https://goo.gl/maps/bmEBStJY7fx)
