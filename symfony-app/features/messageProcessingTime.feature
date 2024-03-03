Feature:
  We want to measure time to process message from queue
  Scenario: Missing afterEvent for previous processed message
    Given The afterEvent has not been called
    When The another message has been processed
    Then We should store one metric
