Feature: Create node, view all nodes



  Background:
    Given I log in as an admin

 @api
  Scenario: Create node, view all nodes


When I visit "node/add/article"
And I fill in "title[0][value]" with "Test article title"
And I fill in "title[0][value]" with a random string "12" characters long
And I press the "Save and publish" button
When I open the links to all homepage posts




