Feature:
  In order to create readable version of webpage
  As a user who use a firefox extension

  Scenario: The button to process webpage is visible
    Given I send POST request to "/web-extension" with body:
    """
url=https://example.com/foo/article&body=lorem-ipsum&title=TitleOfWebPage
    """
    Then I get a response with status "200" and body:
  """
  {"pushToKindleUrl":"https:\/\/fake-push-to-kindle.com"}
  """

  Scenario: Validate request data
    Given I send POST request to "/web-extension" with body:
    """
    foo=abc
    """
    Then I get a response with status "422" and body:
    """
    {
  "type": "https:\/\/symfony.com\/errors\/validation",
  "title": "Validation Failed",
  "status":422,
  "detail": "url: This value should not be blank.\nbody: This value should not be blank.\ntitle: This value should not be blank.",
  "violations": [
    {
      "propertyPath": "url",
      "title": "This value should not be blank.",
      "template": "This value should not be blank.",
      "parameters": {
        "{{ value }}": "null"
      },
      "type": "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
    },
    {
      "propertyPath": "body",
      "title": "This value should not be blank.",
      "template": "This value should not be blank.",
      "parameters": {
        "{{ value }}": "null"
      },
      "type": "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
    },
    {
      "propertyPath": "title",
      "title": "This value should not be blank.",
      "template": "This value should not be blank.",
      "parameters": {
        "{{ value }}": "null"
      },
      "type": "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
    }
  ]
}
    """
  Scenario: Validate url
    Given I send POST request to "/web-extension" with body:
    """
    url=example.com/foo/article&body=lorem-ipsum&title=TitleOfWebPage
    """
    Then I get a response with status "422" and body:
    """
    {
  "type": "https:\/\/symfony.com\/errors\/validation",
  "title": "Validation Failed",
  "status":422,
  "detail": "url: This value is not a valid URL.",
  "violations": [
    {
      "propertyPath": "url",
      "title": "This value is not a valid URL.",
      "template": "This value is not a valid URL.",
      "parameters": {
        "{{ value }}": "\u0022example.com\/foo\/article\u0022"
      },
      "type": "urn:uuid:57c2f299-1154-4870-89bb-ef3b1f5ad229"
    }
  ]
}
    """
