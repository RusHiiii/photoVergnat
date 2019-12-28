Feature: Test the season
  In order to test the basic feature season

  Scenario: I request the season admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/seasons" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the season admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/seasons" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I delete a season
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/season/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Season" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I update a season
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/season/update/1" with the method "PATCH" and with the following payload
    """
    {
      "season": {
        "title": "je suis une légende"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Season" in namespace "WebApp" with attribute "title" equal "je suis une légende" should exist in database

  Scenario: I create a season
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/season/create" with the method "POST" and with the following payload
    """
    {
      "season": {
        "title": "nouvelle saison"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Season" in namespace "WebApp" with attribute "title" equal "nouvelle saison" should exist in database