Feature: startpage
  In order to see the start page
  As an anonymous or logged-in user
  I want to get the first projects on a paginated page

Scenario: First 5 projects in 3 columns on a paginated page
  Given I am on the homepage
  Then I should see 5 ".project" elements
  And each project listed shows title, picture, and text
  And each title is linked to the project detail page
  And each picture is a valid, 200px wide "jpg" thumbnail image

Scenario: Pagination links, I am on first page
  Given I am on the homepage
  Then I should see a ".pagination" element
  And I should see 1 ".prev.disabled" element
  And the ".active" element should contain "1"
  And I should not see a ".next.disabled" element
