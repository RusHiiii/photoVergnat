Feature: Test the category
  In order to test the basic feature category

  Scenario: I request the category admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/categories" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the category admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/categories" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the category page
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/categorie/ceci-est-un-slug_1" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the category gallery page
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/categorie/ceci-est-un-slug_1/photo" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"