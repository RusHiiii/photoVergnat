Feature: Test the user
  In order to test the basic feature user

  Scenario: I request the user account page
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/mon-compte" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the user admin page
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/users" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I edit a user profile
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/user/edit-user/" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "email": "etst@test.fr",
        "lastname": "moi",
        "firstname": "miu"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "User" in namespace "WebApp" with the following data should exist in database
      | attribute  | value        |
      | email      | etst@test.fr |
      | firstname  | miu          |
      | lastname   | moi          |
      | id         | 1            |

  Scenario: I edit a user profile with existing email
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/user/edit-user/" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "email": "admin@orange.fr",
        "lastname": "moi",
        "firstname": "miu"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "User" in namespace "WebApp" with the following data shouldn't exist in database
      | attribute  | value           |
      | email      | admin@orange.fr |
      | firstname  | miu             |
      | lastname   | moi             |
      | id         | 1               |

  Scenario: I edit a user password
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/user/edit-password/1" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "password_first": "ferz456-*",
        "password_second": "ferz456-*"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"

  Scenario: I edit a user password with bad validity
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/user/edit-password/1" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "password_first": "test",
        "password_second": "test"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid data user",
       "code":0,
       "context": [
          "Le champs « mot de passe » n'a pas le bon pattern"
       ],
       "type":"UserInvalidDataException"
    }
    """

  Scenario: I edit a user password with bad user
    Given I load the fixture "simpleUser"
    And I am logged with the user "admin@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/app/user/edit-password/1" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "password_first": "testdezdez*/89",
        "password_second": "testdezdez*/89"
      }
    }
    """
    Then the status code should be "403"

  Scenario: I delete a user
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/user/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "User" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I update a user
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/user/update/1" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "email": "test@uyt.fr",
        "lastname": "test",
        "firstname": "test",
        "roles": ["ROLE_USER"],
        "created": "2019-12-29 17:14:57"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "User" in namespace "WebApp" with the following data should exist in database
      | attribute  | value       |
      | email      | test@uyt.fr |
      | firstname  | test        |
      | lastname   | test        |
      | id         | 1           |

  Scenario: I update a user with bad data
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/user/update/1" with the method "PATCH" and with the following payload
    """
    {
      "user": {
        "email": "test@uyt.fr",
        "lastname": "test",
        "firstname": "",
        "roles": ["ROLE_USER"],
        "created": "2019-12-29 17:14:57"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid data user",
       "code":0,
       "context": [
          "Le champs « prénom » est vide"
       ],
       "type":"UserInvalidDataException"
    }
    """

  Scenario: I create a user
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/user/create" with the method "POST" and with the following payload
    """
    {
      "user": {
        "email": "test@uyt.fr",
        "lastname": "test",
        "firstname": "test",
        "roles": ["ROLE_USER"],
        "password_first": "testge89*7",
        "password_second": "testge89*7"
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "User" in namespace "WebApp" with the following data should exist in database
      | attribute  | value       |
      | email      | test@uyt.fr |
      | firstname  | test        |
      | lastname   | test        |

  Scenario: I create a user with bad data
    Given I load the fixture "simpleUser"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/user/create" with the method "POST" and with the following payload
    """
    {
      "user": {
        "email": "test@uyt.fr",
        "lastname": "",
        "firstname": "test",
        "roles": ["ROLE_USER"],
        "password_first": "testge89*7",
        "password_second": "testge89*7"
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid data user",
       "code":0,
       "context": [
          "Le champs « nom » est vide"
       ],
       "type":"UserInvalidDataException"
    }
    """