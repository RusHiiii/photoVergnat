Feature: Test the tag
  In order to test the basic feature tag

  Scenario: I request the tag admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/tags" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the tag admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/tags" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the display edit modal without login
    Given I load the fixture "completeCategory"
    When I make an XmlHttpRequest to path "/xhr/admin/tag/display/edit/1" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I update a tag
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/tag/update/1" with the method "PATCH" and with the following payload
    """
    {
      "tag": {
        "title": "je suis un tag",
        "type": "type"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Tag" in namespace "WebApp" with the following data should exist in database
      | attribute  | value          |
      | id         | 1              |
      | title      | je suis un tag |
      | type       | type           |

  Scenario: I update a tag with bad data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/tag/update/1" with the method "PATCH" and with the following payload
    """
    {
      "tag": {
        "title": "",
        "type": "type"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid data tag",
       "code":0,
       "context": [
          "Le champs « Titre » est vide"
       ],
       "type":"TagInvalidDataException"
    }
    """

  Scenario: I delete a tag
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/tag/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Tag" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I create a tag
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/tag/create" with the method "POST" and with the following payload
    """
    {
      "tag": {
        "title": "tag titre",
        "type": "type tag"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Tag" in namespace "WebApp" with the following data should exist in database
      | attribute  | value     |
      | title      | tag titre |
      | type       | type tag  |