Feature: userregistration
  In order to be able to login
  As a logged-out user
  I want to click on a link on the login page and register a new user through a webform

Scenario: Anonymous user registers via link on login page 
  Given I am at "/users/login"
  When I click on registration link 
  And I fill in (username, password) and submit the form via "register-button"
  Then I am redirected to the homepage with a success message

Scenario: Anonymous user can login
  Given I logout
  When I click on login link
  And I fill in (username, password) and submit the form via "login-button"
  Then I am logged in
  And I get a welcome message

Scenario: Logged-in user can not log in again
  Given I login
  When I go to "/users/login"
  Then I am redirected to the homepage with an error message

Scenario: Logged-in user can logout
  Given I login
  When I click on logout link
  Then I am logged out
