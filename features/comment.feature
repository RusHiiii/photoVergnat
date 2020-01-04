Feature: Test the comment
  In order to test the basic feature comment

  Scenario: I request the tag admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/comments" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the tag admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/comments" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I delete a comment
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/comment/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Comment" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I create a comment
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/comment/create" with the method "POST" and with the following payload
    """
    {
      "comment": {
        "name": "mon commentaire",
        "message": "mon message",
        "email": "damiens.florebnt@rozero.fr",
        "category": "1"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Comment" in namespace "WebApp" with the following data should exist in database
      | attribute | value                       |
      | name      | mon commentaire             |
      | message   | mon message                 |
      | email     | damiens.florebnt@rozero.fr  |
      | category  | 1                           |

  Scenario: I create a comment with bad data
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/comment/create" with the method "POST" and with the following payload
    """
    {
      "comment": {
        "name": "",
        "message": "mon message",
        "email": "damiens.florebnt@rozero.fr",
        "category": "1"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid comment data",
       "code":0,
       "context": [
          "Le champs « Nom » est vide"
       ],
       "type":"CommentInvalidDataException"
    }
    """

  Scenario: I update a comment
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/comment/update/1" with the method "PATCH" and with the following payload
    """
    {
      "comment": {
        "name": "mon commentaire maj",
        "message": "mon message",
        "email": "damiens.florebnt@rozero.fr",
        "category": "1"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Comment" in namespace "WebApp" with the following data should exist in database
      | attribute | value                       |
      | id        | 1                           |
      | name      | mon commentaire maj         |
      | message   | mon message                 |
      | email     | damiens.florebnt@rozero.fr  |
      | category  | 1                           |

  Scenario: I update a comment with bad data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/comment/update/1" with the method "PATCH" and with the following payload
    """
    {
      "comment": {
        "name": "mon commentaire maj",
        "message": "mon message",
        "email": "damiens.florebnt@rozero.fr",
        "category": "100"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    """
    {
       "message":"Category not found",
       "code":0,
       "context": [
          "Article inexistant"
       ],
       "type":"CategoryNotFoundException"
    }
    """