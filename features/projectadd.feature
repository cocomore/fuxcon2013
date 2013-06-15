Feature: projectadd
  In order to create a project
  As a logged-in user
  I want to click on a link in the user dropdown menu and be able to create a new project in a webform 

Scenario: Anonymous user has no link to create project
  Given I am on the homepage
  When I logout
  Then there is no link on the page to create a project

Scenario: Anonymous user is not allowed to create project
  Given I am on the homepage
  When I logout
  Then I am not allowed to access /projects/add

Scenario: Logged-in user can follow link to create project
  Given I am on the homepage
  When I login
  And I am allowed to access /projects/add
  And I am presented with a webform with (title, picture, about, start_date, end_date, tags)
  And I fill in (title, picture, about, start_date, end_date, tags) and submit the form
  Then a new project is created

