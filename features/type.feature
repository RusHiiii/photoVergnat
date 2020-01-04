Feature: Test the type
  In order to test the basic feature type

  Scenario: I request the type admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/types" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the type admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/types" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I delete a type
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/type/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Type" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I update a type
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/type/update/1" with the method "PATCH" and with the following payload
    """
    {
      "type": {
        "title": "je suis un type"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Type" in namespace "WebApp" with attribute "title" equal "je suis un type" should exist in database

  Scenario: I create a type
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/type/create" with the method "POST" and with the following payload
    """
    {
      "type": {
        "title": "nouveau type"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Type" in namespace "WebApp" with attribute "title" equal "nouveau type" should exist in database