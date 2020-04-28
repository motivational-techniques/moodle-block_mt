@block @block_mt @javascript
Feature: Annotation icons can be clicked
  In order to use the Annotation feature
  As a student
  I need to click the annotation icons

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | Frist    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name           | intro                 | content           |
      | page     | C1     | page1    | Test page name | Test page description | Test page content |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "MT - Motivational Techniques" block
    And I follow "Administration"
    And I check checkbox "Awards" in block mt admin
    And I check checkbox "Goals" in block mt admin
    And I check checkbox "Progress Annotation" in block mt admin
    And I check checkbox "Progress Timeline" in block mt admin
    And I check checkbox "Rankings" in block mt admin
    And I click on "Save changes" "button"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

  Scenario: Test the activity page
    When I follow "Test page name"
    Then I should see "Test page content"

#  Scenario: Click the check mark icon
#    When I click image "check" in block mt
#    Then I should see "tick.png" in the ".activityinstance" "css_element"

# none of these worked, but might be useful later
#    When I click on "//img[@value='1']" "xpath_element" in the ".activityinstance" "css_element"
#    When I click on "//img[@value='1']" "xpath_element" in the "//div[@class='activityinstance']" "xpath_element"

#    And I click on "#id_awards" item in the autocomplete list
#    And I click on "Progress Annotation" "text" in the "//input[@id='id_awards']" "xpath_element"
#    And I click on "Awards" "checkbox"
#    And I click "Progress Annotation" "text" in the ".fcontainer.clearfix" "css_element"
#    And I set the following fields to these values:
#      | id              | checked |
#      | id_p_annotation | true    |
