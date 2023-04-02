Feature:
  In order to create readable version of webpage
  As a user who use a firefox extension

  Scenario: The button to process webpage is visible
    Given I send POST request to "/web-extension" with body:
    """
url=example.com&body=lorem-ipsum&title=TitleOfWebPage
    """
    Then I get a response with status "200" and body:
  """
  {"pushToKindleUrl":"https:\/\/fake-push-to-kindle.com"}
  """
