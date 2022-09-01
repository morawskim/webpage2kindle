Feature:
  In order to create readable version of webpage
  As a user I want to create readable version of a webpage

  Scenario: The button to process webpage is visible
    Given I navigate to "/"
    Then the response should contains button "Process"

  Scenario: Get readable version of page
    Given I navigate to "/"
    When I submit form "Process" with url "http://example.com"
    Then I will be redirected to external page "https://fake-push-to-kindle.com"

  Scenario: Get readable version of page passed as parameter
    Given I navigate to "/?url=https%3A%2F%2Fexample.com%2Fhello-world"
    Then the url field should contain value "https://example.com/hello-world"

  Scenario: URL is empty
    Given I navigate to "/"
    And I follow redirects
    When I submit form "Process" with url ""
    And I will see flash message "URL cannot be empty"

  Scenario: URL is invalid
    Given I navigate to "/"
    And I follow redirects
    When I submit form "Process" with url "foo"
    And I will see flash message "URL "foo" is invalid"

  Scenario: Download firefox extension
    Given I navigate to "/"
    Then the response should contains link "Download firefox extension" to download extension
