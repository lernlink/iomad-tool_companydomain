@tool @tool_companydomain @tool_companydomain_event
Feature: Add manually created users automatically to companies.
  In order to handle all users equally regardless how they were created
  As an admin
  I want that users who did not self-signup are also added to companies based on their email address domain

  Background:
    Given I wait until the page is ready
    And I log in as "admin"
    And I follow "Dashboard" in the user menu
    # I am automatically redirected to the creation of a company, no need to open the create company page manually.
    And I expand all fieldsets
    And I set the field "Long Name" to "Our little company"
    And I set the field "Short Name" to "ourlittlecompany"
    And I set the field "Location (Town/City)" to "Exampletown"
    And I set the field "Select a country" to "Germany"
    And I set the field "List of company domains" to "example.com"
    And I click on "Save new company" "button"

  Scenario: Sacrificial scenario which will fail always on IOMAD for an unknown reason - See https://github.com/moodlehq/moodle-plugin-ci/issues/155
    And I follow "Dashboard" in the user menu

  Scenario: A user without a matching email address domain is created manually and is not added to the company
    When the following "users" exist:
      | username | firstname | lastname | email                |
      | janedoe  | Jane      | Doe      | jane.doe@example.net |
    And I follow "Dashboard" in the user menu
    # As there is only one company, there is no need to select the company which we want to check. This step may be improved.
    And I click on "#UserAdmin-tab" "css_element"
    And I click on "Edit users" "link" in the "#UserAdmin" "css_element"
    Then I should not see "Jane Doe" in the "#region-main" "css_element"

  Scenario: A user with a matching email address domain is created manually and is added to the company automatically
    When the following "users" exist:
      | username | firstname | lastname | email                |
      | johndoe  | John      | Doe      | john.doe@example.com |
    And I follow "Dashboard" in the user menu
    # As there is only one company, there is no need to select the company which we want to check. This step may be improved.
    And I click on "#UserAdmin-tab" "css_element"
    And I click on "Edit users" "link" in the "#UserAdmin" "css_element"
    Then I should see "John Doe" in the "#region-main" "css_element"
