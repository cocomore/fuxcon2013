Feature: projectdetail
  In order to see project details
  As an anonymous or logged-in user
  I want to click in a title on an index page and get a page with title, big picture, dates, and description text formatted from Markdown markup 

Scenario: From the index page a click on a title brings me to a project detail page
  Given I am on the homepage
  When I click on the first project title
  Then I am on a project detail page

Scenario: A project detail page shows title, big picture, dates, and description text
  Given I am on a project detail page
  Then I see content (title, picture, dates, about)
  And the picture has a width of 380px
  And the "about" content is formatted with Markdown
