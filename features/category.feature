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

  Scenario: I delete a category
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/category/remove/1" with the method "DELETE"
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Category" in namespace "WebApp" with attribute "id" equal "1" shouldn't exist in database

  Scenario: I update a category
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/category/update/1" with the method "PATCH" and with the following payload
    """
    {
      "category": {
        "title": "ma categorie",
        "description": "ma description",
        "city": "blois",
        "active": 1,
        "lat": "1",
        "lng": "1",
        "metaDescription": "seo desc",
        "season": "1",
        "tags": [1],
        "photos": [1]
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Category" in namespace "WebApp" with the following data should exist in database
      | attribute   | value        |
      | id          | 1            |
      | title       | ma categorie |
      | city        | blois        |
      | latitude    | 1            |
      | longitude   | 1            |


  Scenario: I update a category with bad data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/category/update/1" with the method "PATCH" and with the following payload
    """
    {
      "category": {
        "title": "",
        "description": "ma description",
        "city": "blois",
        "active": 1,
        "lat": "1",
        "lng": "1",
        "metaDescription": "seo desc",
        "season": "1",
        "tags": [1],
        "photos": [1]
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid category data",
       "code":0,
       "context": [
          "Le champs « Titre » est vide"
       ],
       "type":"CategoryInvalidDataException"
    }
    """

  Scenario: I create a category
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/category/create" with the method "POST" and with the following payload
    """
    {
      "category": {
        "title": "dezdezdzedez",
        "description": "ma description",
        "city": "blois",
        "active": 1,
        "lat": "1",
        "lng": "1",
        "metaDescription": "seo desc",
        "season": "1",
        "tags": [1],
        "photos": [1]
      }
    }
    """
    Then the status code should be "200"
    And the content type should be "application/json"
    And Object "Category" in namespace "WebApp" with the following data should exist in database
      | attribute   | value        |
      | title       | dezdezdzedez |
      | city        | blois        |
      | latitude    | 1            |
      | longitude   | 1            |

  Scenario: I create a category with bad data
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/category/create" with the method "POST" and with the following payload
    """
    {
      "category": {
        "title": "dezdezdzedzedze",
        "description": "ma description",
        "city": "",
        "active": 1,
        "lat": "1",
        "lng": "1",
        "metaDescription": "seo desc",
        "season": "1",
        "tags": [1],
        "photos": [1]
      }
    }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid category data",
       "code":0,
       "context": [
          "Le champs « Ville » est vide"
       ],
       "type":"CategoryInvalidDataException"
    }
    """