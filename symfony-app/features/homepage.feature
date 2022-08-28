Feature:
  In order to create readable version of webpage
  As a user I want to create readable version of a webpage

  Scenario: The button to process webpage is visible
    Given I navigate to "/"
    Then the response should contains button "Process"


  Scenario: Get readable version of page
    Given I navigate to "/"
    When I submit form "Process" with url "http://example.com"
    Then I will be redirected to external page

  Scenario: Download firefox extension
    Given I navigate to "/"
    Then the response should contains link "Download firefox extension"
