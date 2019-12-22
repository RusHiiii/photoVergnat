Feature: Test the main app
  In order to test the basic feature of the app

  Scenario: I request the main page
    Given I load the fixture "completeCategory"
    When I request the path "/" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"
