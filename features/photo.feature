Feature: Test the photo
  In order to test the basic feature photo

  Scenario: I request the category admin page without login
    Given I load the fixture "completeCategory"
    When I make an HttpRequest to path "/admin/photos" with the method "GET"
    Then the status code should be "302"
    And the content type should be "text/html"

  Scenario: I request the category admin page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/photos" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I delete a photo
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Photo" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database
    And File "test_photovergnat_1.jpeg" in folder "uploads" shouldn't exist

  Scenario: I update a photo
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/update/1" with the method "PATCH" and with the following payload
    """
    {
      "title": "ma super photo",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "200"
    And Object "Photo" in namespace "WebApp" with the following data should exist in database
      | attribute | value          |
      | id        | 1              |
      | title     | ma super photo |

  Scenario: I update a photo with bad data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/update/1" with the method "PATCH" and with the following payload
    """
    {
      "title": "",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "400"
    And the content should have the following content
    """
    {
       "message":"Invalid photo data",
       "code":0,
       "context": [
          "Le champs « Titre » est vide"
       ],
       "type":"PhotoInvalidDataException"
    }
    """

  Scenario: I update a photo with file attached
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/update/1" with the method "PATCH" and with the following payload and file attached
    """
    {
      "title": "ma super photo",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "200"
    And File "test_photovergnat_1.jpeg" in folder "uploads" shouldn't exist
    And Object "Photo" in namespace "WebApp" with the following data should exist in database
      | attribute | value          |
      | id        | 1              |
      | title     | ma super photo |

  Scenario: I create a photo
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/create" with the method "POST" and with the following payload and file attached
    """
    {
      "title": "ma super photo 2",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "200"
    And Object "Photo" in namespace "WebApp" with the following data should exist in database
      | attribute | value            |
      | title     | ma super photo 2 |
      | type      | 1                |

  Scenario: I create a photo with bad data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/create" with the method "POST" and with the following payload and file attached
    """
    {
      "title": "",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "400"
    And the content should have the following content
    """
    {
       "message":"Invalid photo data",
       "code":0,
       "context": [
          "Le champs « Titre » est vide"
       ],
       "type":"PhotoInvalidDataException"
    }
    """

  Scenario: I create a photo with no file
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/photo/create" with the method "POST" and with the following payload
    """
    {
      "title": "test faux",
      "format": 1,
      "tags": [1]
    }
    """
    Then the status code should be "400"
    And the content should have the following content
    """
    {
       "message":"Invalid photo data",
       "code":0,
       "context": [
          "Le champs « photo » est obligatoire"
       ],
       "type":"PhotoInvalidDataException"
    }
    """