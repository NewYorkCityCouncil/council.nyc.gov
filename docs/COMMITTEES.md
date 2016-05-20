# Committees

Committees are a custom post type, similar to pages.

### Editing a Committee

To add/edit a Committee, go to the main site's WordPress Admin > Committees.

* **Title** - should be prefixed with "Committee on" (for context on other pages & lists)
* **Permalink** - should be manually set to not include the `committee-on-` prefix
* **Excerpt** *(optional)* - appears as a subheading under the Committee title (the excerpt also appears in search results, which defaults to the first ~55 words of the content if the excerpt is empty)
* **Committee Members** - set a membership status for each Council Member (Members will be grouped by their status and ordered by district number within those groups, which appear in the following order: Chair, Co-Chair, Vice Chair, Vice Co-Chair, Secretary, Treasurer, Member)
* **Attributes**
    * **Parent** - if set, makes the Committee a Subcommittee of the selected parent
    * **Order** - order by which the Committee appears in lists (falls back to alphabetical)

### Committees List

A regular page using the "Committees List" template will display a list off all Committees. 
