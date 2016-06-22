# Content

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

Here are some common HTML tags:

#### Link
```
<a href="http://example.com/" title="Description of the Link">link text</a>
```
The `href` is the hyperlink reference known as the URL or address. The `title` is descriptive text that describes or titles the link, required by web standards for accessibility.

#### Image
```
<img src="..." alt="A description of the image.">
```
The `src` is the location of the image. The `alt` is descriptive text that provides a clear alternative for screen reader users, required by web standards for accessibility

#### Heading
```
<h2>Heading Here</h2>
```
There are six heading tags: `<h1>`, `<h2>`, `<h3>`, `<h4>`, `<h5>`, and `<h6>`. The `<h1>` tag is the largest heading and identifies the most important content—typically reserved for the page title and rarely used in content. The `<h6>` tag is the smallest heading and identifies the least important content.

Choose the appropriate heading tag to add hierarchy to your content. **Do not** choose a tag by the way it looks (i.e. its size). To change the visual appearance of a heading, add a class to the tag:

```
<h6 class="header-xlarge">Looks like h2</h6>
```
* `header-xxlarge` (looks like h1)
* `header-xlarge` (looks like h2)
* `header-large` (looks like h3)
* `header-medium` (looks like h4)
* `header-small` (looks like h5)
* `header-tiny` (looks like h6)

#### Paragraph
```
<p>This is a paragraph. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.</p>
```
Paragraph tags aren't necessary most of the time. WordPress automatically changes double line breaks in the content into HTML paragraphs (<p>...</p>). However, this can sometimes have annoying side affects when writing raw HTML. Automatically-inserted paragraph tags can adversely affect page layouts and Foundation components. To prevent auto-p tags, remove white space or wrap content in `<div>` tags.

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

#### Ordered List (Numbered)
```
<ol>
  <li>item text</li>
  <li>more item text</li>
  <li>another item</li>
</ol>
```

#### Unordered List (Bulleted)
```
<ul>
  <li>First item</li>
  <li>Second item</li>
  <li>Third item</li>
</ul>
```

#### Blockquotes
```
<blockquote>
  Participatory budgeting puts power back into the hands of the people.
  <cite>Speaker Melissa Mark-Viverito</cite>
</blockquote>
```

#### Line Break
```
<br />
```

#### Horizontal Rule
```
<hr />
```

#### Embedded Content
```
<div class="flex-video widescreen">
  <iframe src="..." width="560" height="315" frameborder="0" allowfullscreen></iframe>
</div>
```
You can embed content such as videos and maps in iframes. For these to be responsive and work well on all devices, they must be wrapped in a `<div>` with the `flex-video` class. The `widescreen` class changes the the ratio of the iframe from 4:3 to 16:9. If the `src` of the iframe is YouTube or Vimeo, a container with the appropriate classes is added dynamically.

## Layout

columns
