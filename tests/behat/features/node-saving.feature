Feature: Node saving

  @api
  Scenario: Node Saving
    Given I am logged in as a user with the "administrator" role
    And I visit "/"
    When I visit "node/add/article"
    And I fill in "title[0][value]" with "First Test Article"
    And I press the "Save and publish" button
    And I visit "/"
    Then I see the text "First Test Article"
