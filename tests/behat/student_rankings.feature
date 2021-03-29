@block @block_mt
Feature: A student achieves a ranking
    In order to earn a rank in a course
    As a student
    I need to participate

  Scenario: Basic particiaption in the course
    Given the following "users" exist:
      | username | firstname | lastname| email              |
      | test_1   | test      | one     | test_1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
      