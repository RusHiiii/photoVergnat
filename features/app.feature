Feature: Test the main app
  In order to test the basic feature of the app

  Scenario: I request the main page
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the contact page
    When I make an HttpRequest to path "/contact" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the about page
    When I make an HttpRequest to path "/a-propos" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"
