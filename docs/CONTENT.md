# Content Organization

When adding content, consider that there are very key differences between Posts and Pages.

## Posts

**Posts are for timestamped news and announcements** such as:
* "Today we passed a law to help New Yorkers..."
* "The Fiscal Year 20XX Budget will include..."

Posts are listed in reverse chronological order in places like the sidebar, category/tag archives, and RSS feeds.

## Pages

**Pages are for definitive, static information** such as:
* "This is how we pass laws..."
* "This is how the budget process works..."

All pages should have a clearly defined user goal. Be cautious when including date-specific information in pages. Doing so may diminish the effectiveness of the page. For example, a page about how the budget process works should not include information about the current budget cycle. Does the page exist for users to learn about the process or to find out what's in next year's budget? If a page's content will need to be changed tomorrow, maybe it should instead be a post.

### Page Templates

Page templates are used to change the layout and function of a page. When editing a page, you can use the Page Attributes > Templates menu (in the righthand sidebar) to choose which of the following templates will be used to render the page.

* **Default Template**
* **Caucuses List** - _lists all Caucuses alongside the page content_
* **Committees List** - _lists all Committees alongside the page content_
* **Districts List** - _displays a searchable table of Members/Districts alongside the page content_
* **Image Header** - _the page title is displayed over its Featured Image as a full-width background_
* **Initiatives List** - _lists all Initiatives alongside the page content_
* **PB Districts List** - _displays alongside the page content a District lookup widget and a list of Districts that have PB projects_
* **PB Results** - _displays page content followed by a list of winning PB projects ordered by District_
* **PB Sidebar** - _displays the PB Sidebar widgets alongside the page content_
* **Raw HTML, Full-width, No Header** - _a blank template for hand-coding a custom layout_

## Markup

The WordPress content editor provides a visual and a text view. The visual view shows formatted content. The text view allows you to write raw HTML and have more control over content design and structure. If you choose to write HTML, be careful switching to the visual editor, as it may clobber your work or have undesired results.

The most common HTML tags are:

#### Link

```
<a href="http://example.com/" title="Description of the Link">link text</a>
```

The `href` is the hyperlink reference known as the URL or address. The `title` is the descriptive text that describes or titles the link, required by web standards for accessibility.

#### Image

```
<img src="..." />
```

#### Headings

```
<h2>Heading Here</h2>
```

There are six heading tags: `<h1>`, `<h2>`, `<h3>`, `<h4>`, `<h5>`, and `<h6>`. The `<h1>` tag is the largest heading and identifies the most important content—typically reserved for the page title and rarely used in content. The `<h6>` tag is the smallest heading and identifies the least important content.

Choose the appropriate heading tag to add hierarchy to your content. **Do not** choose a tag by the way it looks (i.e. its size). To change the visual appearance of a heading, the following classes may be applied to the tag:

* `header-xxlarge` looks like h1
* `header-xlarge` looks like h2
* `header-large` looks like h3
* `header-medium` looks like h4
* `header-small` looks like h5
* `header-tiny` looks like h6

```
<h6 class="header-xlarge">Looks like h2</h6>
```

#### Paragraph

```
<p>Hi. This is a paragraph.</p>
```

WordPress automatically adds `<p>` tags...

#### Bold

```
<strong>bold text</strong>
```

Don't use `<b>`.

#### Italic

```
<em>italic text</em>
```

Don't use `<i>`.

#### Lists
```
<ol>
  <li>item text</li>
  <li>item text</li>
</ol>
```
ordered/numbered

```
<ul>
  <li>item text</li>
  <li>item text</li>
</ul>
```
unordered/bulleted

#### Blockquotes
```
<blockquote>
  <cite>
  </cite>
</blockquote>
```

#### Line Break
`<br />`

#### Horizontal Rule
`<hr />`

#### Code
`<pre>` and `<code>`

#### Embedded Content

iframe

_Note: All HTML tags must be closed. If you open a tag you must close it with a closing tag. The only exceptions are self-closing tags: `<img src="..." />`, `<br />`, `<hr />`._

## Layout

columns
