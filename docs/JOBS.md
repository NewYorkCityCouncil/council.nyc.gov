# Jobs

The Job Opportunities section is its own site in the WordPress multisite network. This is so that editors of Jobs content do not have access to the content of other Divisions/Districts. This also simplifies the admin so that editors can more easily perform their tasks. The Jobs site uses the "NYCC Jobs" theme (`wp-nycc-jobs`), a child theme of the "New York City Council" theme (`wp-nycc`). _Note: Super Admins have access to all network sites._

## Jobs Front Page

When setting up the Jobs site, a page titled "Job Opportunities" must be created for the front page. Then, in the Job site's Admin > Settings > Reading, the "Front page displays" settings should set this page as the "Front page." The "Posts page" should be left null (blog posts are disabled for this theme).

## Jobs Opportunities (Pages)

In the "NYCC Jobs" theme, WordPress _Pages_ are used to create job postings. They function just as regular Posts do, except that they also use custom taxonomy called "Job Divisions."

When adding a new page, the Job Division is set to "N/A (regular page)" by default. Leave this as-is for regular pages (e.g. the front page or the _Anti-Discrimination & Harassment Policy_).

To turn a regular page into a job posting, simply change this value to the correct Council Division. Then the page will appear in the Jobs List widget.

To edit the available terms in the Job Divisions taxonomy, go to the Job site's Admin > Pages > Job Divisions.

## Jobs List Widget

The Jobs List widget should be included in the Sidebar widget area in the Job site's Admin > Appearance > Widgets. This widget displays a list of job postings grouped by Council Divisions.
