@new
Feature: projectedit
  In order to edit a project
  As project owner or admin user
  I want to click on a button on a project detail page be able to edit the project in a webform

Scenario: For project owners, on a project detail page there is an edit button
  Given I am on the detail page of my first project
  Then there is a button to edit the project
  And I am allowed to access the project edit page

Scenario: For admins, on a project detail page there is an edit button
  Given I am on the detail page of a project by someone else
  And I am admin
  Then there is a button to edit the project
  And I am allowed to access the project edit page

Scenario: For other users, on a project detail page there is no edit button
  Given I am on the detail page of a project by someone else
  And I am not admin
  Then there is no button to edit the project
  And I am not allowed to access the project edit page

Scenario: On the project edit page, I can change fields and upload pictures
  Given I am on the detail page of my first project
  When I change any of (title, start_date, end_date, picture, about)
  Then my changes are saved
