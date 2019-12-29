Feature: Test the information
  In order to test the basic feature of the info page

  Scenario: I request the contact page
    When I make an HttpRequest to path "/contact" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the about page
    When I make an HttpRequest to path "/a-propos" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the admin statistics page with login
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an HttpRequest to path "/admin/statistics" with the method "GET"
    Then the status code should be "200"
    And the content type should be "text/html"

  Scenario: I request the admin statistics data
    Given I load the fixture "completeCategory"
    And I am logged with the user "damiens.florent@orange.fr"
    When I make an XmlHttpRequest to path "/xhr/admin/statistics" with the method "GET"
    Then the status code should be "200"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "photo":[
          {
             "month":"Aug",
             "count":"0"
          },
          {
             "month":"Sep",
             "count":"0"
          },
          {
             "month":"Oct",
             "count":"0"
          },
          {
             "month":"Nov",
             "count":"0"
          },
          {
             "month":"Dec",
             "count":"2"
          }
       ],
       "category":[
          "0",
          "1"
       ]
    }
    """

  Scenario: I request the send contact message
    When I make an XmlHttpRequest to path "/xhr/app/information/contact/send" with the method "POST" and with the following payload
    """
     {
        "mail": {
          "subject": "ceci est un sujet",
          "choice": "1",
          "message": "ceci est un vrai message de moi",
          "email": "damiens.florent@gmail.com"
        }
     }
    """
    Then the status code should be "200"
    And the content type should be "application/json"

  Scenario: I request the send contact message with bad content
    When I make an XmlHttpRequest to path "/xhr/app/information/contact/send" with the method "POST" and with the following payload
    """
     {
        "mail": {
          "subject": "",
          "choice": "1",
          "message": "ceci est un vrai message de moi",
          "email": "damiens.florentgmail.com"
        }
     }
    """
    Then the status code should be "400"
    And the content type should be "application/json"
    And the content should have the following content
    """
    {
       "message":"Invalid information data",
       "code":0,
       "context": [
          "Le champs « Sujet » est vide",
          "Le champs « Email » est invalide"
       ],
       "type":"InformationInvalidDataException"
    }
    """